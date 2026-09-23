# Deployment (VPS over SSH, automated from GitHub)

Every push to `main` triggers `.github/workflows/deploy.yml`, which SSHes into
the server, pulls the new code, and runs `deploy/deploy.sh` (install, build,
migrate, cache, restart workers). This page covers the one-time server setup and
the GitHub secrets that make it run.

Secrets never leave the server: the pipeline does not send `.env` or credentials
over the wire, and `.env` is git-ignored.

---

## 1. Provision the server (one time)

On a fresh Ubuntu 22.04+ box, install PHP 8.2+, Composer, Node, a database and a
web server:

```bash
sudo apt update
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mbstring php8.2-xml \
  php8.2-mysql php8.2-bcmath php8.2-curl php8.2-zip php8.2-intl unzip git nginx mysql-server
curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash - && sudo apt install -y nodejs
```

Create the database and a user:

```bash
sudo mysql -e "CREATE DATABASE whatsapp_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'whatsapp'@'localhost' IDENTIFIED BY 'CHANGE_ME';"
sudo mysql -e "GRANT ALL ON whatsapp_platform.* TO 'whatsapp'@'localhost'; FLUSH PRIVILEGES;"
```

## 2. First checkout and .env

```bash
sudo mkdir -p /var/www/whatsapp && sudo chown $USER:www-data /var/www/whatsapp
git clone https://github.com/Tresto2025/Whatsapp_product.git /var/www/whatsapp
cd /var/www/whatsapp
cp .env.example .env
```

Edit `.env` for production — at minimum:

```
APP_NAME="WhatsApp Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=whatsapp_platform
DB_USERNAME=whatsapp
DB_PASSWORD=CHANGE_ME

QUEUE_CONNECTION=database

SUPER_ADMIN_EMAIL=you@your-domain.com
SUPER_ADMIN_PASSWORD=a-strong-password
```

Then initialise:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan key:generate      # writes APP_KEY into .env — back this up; it decrypts stored WhatsApp tokens
php artisan migrate --force
php artisan db:seed --force   # creates the super admin
sudo chown -R www-data:www-data storage bootstrap/cache
```

## 3. Web server

`/etc/nginx/sites-available/whatsapp` (symlink into `sites-enabled`, then
`sudo nginx -t && sudo systemctl reload nginx`):

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/whatsapp/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

Add HTTPS with `sudo certbot --nginx -d your-domain.com` (Meta requires HTTPS
for the webhook).

## 4. Queue worker (needed for campaigns and inbound processing)

`QUEUE_CONNECTION=database` means jobs (`ProcessInboundWhatsAppMessage`,
`SendCampaign`) run on a worker, not inline. Keep one running with systemd:

`/etc/systemd/system/whatsapp-worker.service`:

```ini
[Unit]
Description=WhatsApp Platform queue worker
After=network.target

[Service]
User=www-data
Restart=always
WorkingDirectory=/var/www/whatsapp
ExecStart=/usr/bin/php artisan queue:work --sleep=1 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable --now whatsapp-worker
```

`deploy/deploy.sh` runs `php artisan queue:restart` so the worker picks up new
code on each deploy.

## 5. Give the deploy user SSH access

The pipeline logs in as a deploy user that can write to `/var/www/whatsapp` and
run the deploy script. Generate a dedicated key pair (no passphrase) and
authorise it:

```bash
ssh-keygen -t ed25519 -f deploy_key -N ""
# append deploy_key.pub to the deploy user's ~/.ssh/authorized_keys on the server
```

Keep the **private** key (`deploy_key`) for the next step; never commit it.

## 6. Add the GitHub secrets

In the repo: **Settings → Secrets and variables → Actions → New repository
secret**. Add:

| Secret        | Value |
|---------------|-------|
| `SSH_HOST`    | server hostname or IP |
| `SSH_USER`    | the deploy user |
| `SSH_KEY`     | contents of the private `deploy_key` |
| `DEPLOY_PATH` | `/var/www/whatsapp` |
| `SSH_PORT`    | optional; defaults to 22 |

## 7. Deploy

Push to `main` (or run the **Deploy** workflow from the Actions tab). The
workflow connects, resets the checkout to `origin/main`, and runs
`deploy/deploy.sh`. Watch it under the repo's **Actions** tab.

## 8. Point Meta at the webhook

In each tenant's Meta app → WhatsApp → Configuration, set the callback URL to
`https://your-domain.com/api/webhook` and paste the verify token shown on that
tenant's WhatsApp Connection page.

---

### Notes

- **`APP_KEY` is critical.** It encrypts every stored WhatsApp token. Back it up
  with the database; losing it forces every tenant to reconnect.
- **CI vs Deploy.** `.github/workflows/ci.yml` builds and tests every push and
  PR; `deploy.yml` deploys `main`. A red CI run does not block the deploy job —
  add `needs:`/branch protection if you want it to.
- **Rollback.** `git reset --hard <previous-sha>` in `DEPLOY_PATH` then
  `bash deploy/deploy.sh`, or re-run the Deploy workflow on an earlier commit.
