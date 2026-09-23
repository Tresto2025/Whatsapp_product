#!/usr/bin/env bash
#
# Runs ON THE SERVER, from the application root, after the new code is checked
# out. Idempotent: safe to re-run. The GitHub Actions deploy workflow calls it
# over SSH, and you can run it by hand for a manual deploy.
#
# Assumes: PHP + Composer + Node/npm installed, a configured .env in place, and
# a database reachable from it. It never touches .env, so secrets stay on the
# server and are never in the repo or the CI logs.

set -euo pipefail

echo "==> Deploying $(git rev-parse --short HEAD 2>/dev/null || echo 'unknown')"

# Take the app down only for the moment it takes to migrate and rebuild caches.
php artisan down --render="errors::503" --retry=15 || true
trap 'php artisan up || true' EXIT

echo "==> Installing PHP dependencies"
composer install --no-interaction --prefer-dist --no-progress --no-dev --optimize-autoloader

echo "==> Building front-end assets"
npm ci
npm run build

echo "==> Running migrations"
php artisan migrate --force

echo "==> Refreshing caches"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> Restarting queue workers"
php artisan queue:restart || true

php artisan up
trap - EXIT
echo "==> Deploy complete"
