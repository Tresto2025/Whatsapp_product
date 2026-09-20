# Running the app locally

Verified on Windows 11 with [scoop](https://scoop.sh). The stack is PHP 8.3, MySQL and Node 24.

## 1. Toolchain

Laravel 10 supports PHP 8.1–8.3, so pin 8.3 rather than scoop's default `php` (8.5).

```sh
scoop bucket add versions
scoop bucket add extras
scoop install php83 composer mysql
scoop install vcredist2022        # php.exe will not start without the VC++ runtime
```

### PHP extensions

A scoop PHP ships without a `php.ini`, so no extensions load. Create one:

```sh
cd ~/scoop/apps/php83/current
cp php.ini-production php.ini
```

and append:

```ini
extension_dir = "ext"
extension=openssl
extension=mbstring
extension=fileinfo
extension=curl
extension=zip
extension=pdo_mysql
extension=mysqli
extension=pdo_sqlite
extension=sqlite3
extension=gd
extension=intl
extension=sodium
extension=exif
zend_extension=opcache

memory_limit = 512M
date.timezone = UTC
```

Check with `php -m`. Note this file is not persisted across `scoop update php83`.

## 2. Database

```sh
mysqld --console          # leave running, or install it as a service
mysql -u root -e "CREATE DATABASE whatsapp_product CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE DATABASE whatsapp_product_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

`whatsapp_product_test` is the database `phpunit.xml` points at, so tests never touch dev data.

## 3. Application

```sh
composer install
cp .env.example .env
php artisan key:generate
```

Set at least these in `.env`:

```
DB_DATABASE=whatsapp_product
DB_USERNAME=root
DB_PASSWORD=
SUPER_ADMIN_EMAIL=superadmin@example.com
SUPER_ADMIN_PASSWORD=<something you choose>
```

Then build the schema and seed the default tenant plus the platform super admin:

```sh
php artisan migrate:fresh --seed
```

`migrate:fresh` builds the entire schema from the migrations — the SQL dump is no longer
needed. Every baseline migration is guarded with `Schema::hasTable()`, so running `migrate`
against a legacy dump-loaded database only adds the tenancy columns.

## 4. Front-end assets

Without these the Breeze/Blade pages throw `Vite manifest not found`:

```sh
npm install
npm run build      # or: npm run dev
```

## 5. Run it

```sh
php artisan serve         # http://127.0.0.1:8000
php artisan test
```

## Notes

- `composer install` can take 20+ minutes on Windows with Defender enabled; it is not hung.
- The WhatsApp webhook needs a public URL. Until Phase 2 adds per-tenant credentials, it
  reads the `WHATSAPP_*` keys in `.env` and verifies `X-Hub-Signature-256` against
  `WHATSAPP_APP_SECRET`.
- `QUEUE_CONNECTION=sync` runs jobs inline, so no queue worker is needed locally yet.
