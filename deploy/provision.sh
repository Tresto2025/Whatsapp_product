#!/usr/bin/env bash
#
# One-time server provisioning, run ON THE SERVER as root from the app root.
# Installs the stack, creates the database, writes a production .env (only if
# missing, so APP_KEY is never regenerated), builds, migrates, seeds, and wires
# up nginx and the queue worker. Idempotent: safe to re-run.
#
# Triggered by .github/workflows/provision.yml. Not part of the normal deploy.

set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/whatsapp}"
APP_URL="${APP_URL:-http://72.61.237.75}"
DB_NAME="${DB_NAME:-whatsapp_platform}"
DB_USER="${DB_USER:-whatsapp}"

echo "==> Installing system packages"
export DEBIAN_FRONTEND=noninteractive
# Remove any broken third-party PHP source a previous run may have added
# (the ondrej PPA has no release for some Ubuntu versions and breaks apt).
rm -f /etc/apt/sources.list.d/*ondrej*.list /etc/apt/sources.list.d/*ondrej*.sources 2>/dev/null || true
apt-get update -y
# Use the distribution's own PHP packages (unversioned metapackages) so this
# works on whatever Ubuntu the server runs.
apt-get install -y curl unzip git \
  php php-fpm php-cli php-mbstring php-xml \
  php-mysql php-bcmath php-curl php-zip php-intl php-gd \
  nginx mysql-server

# Detect the installed PHP version for the FPM service and socket path.
PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
echo "==> Using PHP ${PHP_VER}"
systemctl enable --now "php${PHP_VER}-fpm"

if ! command -v composer >/dev/null 2>&1; then
  echo "==> Installing Composer"
  curl -sS https://getcomposer.org/installer | php
  mv composer.phar /usr/local/bin/composer
fi

if ! command -v node >/dev/null 2>&1; then
  echo "==> Installing Node 20"
  curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
  apt-get install -y nodejs
fi

echo "==> Ensuring MySQL is running"
systemctl enable --now mysql

cd "$APP_DIR"

# .env is created once and then left alone — regenerating APP_KEY would make
# every stored WhatsApp token undecryptable.
if [ ! -f .env ]; then
  echo "==> Creating .env"
  DB_PASS="$(openssl rand -hex 16)"
  cp .env.example .env
  {
    echo
    echo "APP_ENV=production"
    echo "APP_DEBUG=false"
    echo "APP_URL=${APP_URL}"
    echo "DB_CONNECTION=mysql"
    echo "DB_HOST=127.0.0.1"
    echo "DB_PORT=3306"
    echo "DB_DATABASE=${DB_NAME}"
    echo "DB_USERNAME=${DB_USER}"
    echo "DB_PASSWORD=${DB_PASS}"
    echo "QUEUE_CONNECTION=database"
    echo "SESSION_DRIVER=database"
    echo "CACHE_STORE=database"
    echo "SUPER_ADMIN_EMAIL=admin@example.com"
    echo "SUPER_ADMIN_PASSWORD=$(openssl rand -hex 8)"
  } >> .env
  NEW_ENV=1
else
  echo "==> Reusing existing .env"
  DB_PASS="$(grep -E '^DB_PASSWORD=' .env | head -1 | cut -d= -f2-)"
  NEW_ENV=0
fi

echo "==> Ensuring database and user"
mysql <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

echo "==> Installing dependencies and building"
composer install --no-interaction --prefer-dist --no-progress --no-dev --optimize-autoloader
npm ci
npm run build

if [ "$NEW_ENV" = "1" ]; then
  echo "==> Generating APP_KEY"
  php artisan key:generate --force
fi

echo "==> Migrating and seeding"
php artisan migrate --force
php artisan db:seed --force || true

echo "==> Setting permissions"
chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

echo "==> Caching config/routes/views"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Configuring nginx"
cat > /etc/nginx/sites-available/whatsapp <<NGINX
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name _;
    root ${APP_DIR}/public;

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        fastcgi_pass unix:/run/php/php${PHP_VER}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
NGINX
ln -sf /etc/nginx/sites-available/whatsapp /etc/nginx/sites-enabled/whatsapp
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx

echo "==> Installing the queue worker service"
cat > /etc/systemd/system/whatsapp-worker.service <<UNIT
[Unit]
Description=WhatsApp Platform queue worker
After=network.target mysql.service

[Service]
User=www-data
Restart=always
WorkingDirectory=${APP_DIR}
ExecStart=/usr/bin/php artisan queue:work --sleep=1 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
UNIT
systemctl daemon-reload
systemctl enable --now whatsapp-worker
systemctl restart whatsapp-worker

echo "======================================================"
echo "Provisioning complete. App should be live at ${APP_URL}"
if [ "$NEW_ENV" = "1" ]; then
  echo "Super admin: $(grep -E '^SUPER_ADMIN_EMAIL=' .env | cut -d= -f2-) / $(grep -E '^SUPER_ADMIN_PASSWORD=' .env | cut -d= -f2-)"
  echo "(Change this password after first login.)"
fi
echo "======================================================"
