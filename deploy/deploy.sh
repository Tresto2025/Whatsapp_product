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
# Clear any partial node_modules from an interrupted run (npm ci can hit
# ENOTEMPTY otherwise), then install cleanly.
rm -rf node_modules
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

echo "==> Health check"
# The app listens on its own port (80/443 belong to another app's Docker proxy
# on this shared VPS). Must match APP_PORT in provision.sh.
APP_PORT="${APP_PORT:-8081}"
sleep 2
CODE="$(curl -s -o /dev/null -w '%{http_code}' "http://127.0.0.1:${APP_PORT}/login" || echo 000)"
echo "GET :${APP_PORT}/login -> HTTP ${CODE}"
if [ "$CODE" != "200" ]; then
  echo "WARNING: health check did not return 200"
  echo "---- listener on :${APP_PORT} ----"
  ss -tlnp 2>/dev/null | grep -E ":${APP_PORT}\b" || echo "nothing listening"
  nginx -t 2>&1 | tail -2 || true
fi

# Report only; a dead worker means campaigns and inbound messages (including
# chatbot auto-replies) silently stop processing.
WORKER="$(systemctl is-active whatsapp-worker 2>/dev/null || true)"
echo "whatsapp-worker: ${WORKER:-unknown}"
if [ "$WORKER" != "active" ] && [ "$WORKER" != "activating" ]; then
  echo "WARNING: queue worker is not running — check 'systemctl status whatsapp-worker'"
fi

echo "==> Deploy complete"
