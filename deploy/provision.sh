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
# Ports 80/443 on this shared VPS belong to another app's Docker proxy, so this
# app gets its own port. Must match APP_PORT in deploy.sh.
APP_PORT="${APP_PORT:-8081}"
APP_URL="${APP_URL:-http://72.61.237.75:${APP_PORT}}"
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

# Rebuild a clean, single-line .env each run from .env.example, preserving the
# existing APP_KEY and DB password when present (so we never orphan encrypted
# data or drift the DB user's password from what the app sends). Rebuilding
# avoids the duplicate-key drift that plagues append-only edits.
ensure_env() {
  local key="$1" val="$2"
  if grep -qE "^${key}=" .env; then
    sed -i "s|^${key}=.*|${key}=${val}|" .env
  else
    echo "${key}=${val}" >> .env
  fi
}

OLD_KEY=""
OLD_PASS=""
if [ -f .env ]; then
  OLD_KEY="$(grep -E '^APP_KEY=base64:.+' .env | tail -1 | cut -d= -f2- || true)"
  OLD_PASS="$(grep -E '^DB_PASSWORD=.+' .env | tail -1 | cut -d= -f2- || true)"
fi
DB_PASS="${OLD_PASS:-$(openssl rand -hex 16)}"

echo "==> Writing .env"
cp .env.example .env
ensure_env APP_ENV production
ensure_env APP_DEBUG false
ensure_env APP_URL "${APP_URL}"
ensure_env DB_CONNECTION mysql
ensure_env DB_HOST 127.0.0.1
ensure_env DB_PORT 3306
ensure_env DB_DATABASE "${DB_NAME}"
ensure_env DB_USERNAME "${DB_USER}"
ensure_env DB_PASSWORD "${DB_PASS}"
ensure_env QUEUE_CONNECTION database
ensure_env SESSION_DRIVER file
ensure_env CACHE_DRIVER file
grep -q '^SUPER_ADMIN_EMAIL=' .env || echo "SUPER_ADMIN_EMAIL=admin@example.com" >> .env
grep -q '^SUPER_ADMIN_PASSWORD=' .env || echo "SUPER_ADMIN_PASSWORD=$(openssl rand -hex 8)" >> .env
[ -n "$OLD_KEY" ] && ensure_env APP_KEY "base64:${OLD_KEY}"

echo "==> Ensuring database and user"
# The app connects over TCP (DB_HOST=127.0.0.1), which MySQL matches as a
# different host from 'localhost' (the socket). Grant both so it works either
# way.
mysql <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL

echo "==> Installing dependencies and building"
composer install --no-interaction --prefer-dist --no-progress --no-dev --optimize-autoloader
# Clear any partial node_modules from an interrupted run (npm ci can hit
# ENOTEMPTY otherwise), then install cleanly.
rm -rf node_modules
npm ci
npm run build

if ! grep -qE '^APP_KEY=base64:.+' .env; then
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
    listen ${APP_PORT};
    listen [::]:${APP_PORT};
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
# Without a delay, the burst of clean exits a deploy causes (queue:restart,
# maintenance mode) trips systemd's 5-starts-in-10s limit and the worker stays
# dead until someone runs reset-failed.
RestartSec=5
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
echo "Super admin email: $(grep -E '^SUPER_ADMIN_EMAIL=' .env | tail -1 | cut -d= -f2-)"
echo "(Super admin password is in .env on the server; change it after first login.)"
echo "======================================================"
