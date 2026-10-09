#!/usr/bin/env bash
# Simple in-place deploy. Run as the app user from the project root:  bash deploy/deploy.sh [branch]
set -euo pipefail
cd "$(dirname "$0")/.."
BRANCH="${1:-main}"

echo "==> Safety backup"
php artisan travelorio:backup || echo "(no backup taken – first deploy?)"

php artisan down --retry=30 || true
trap 'php artisan up' EXIT

echo "==> Code"
git fetch origin "$BRANCH"
git checkout "$BRANCH"
git reset --hard "origin/$BRANCH"

echo "==> PHP dependencies"
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

echo "==> Front-end build"
if command -v npm >/dev/null; then npm ci && npm run build; else echo "npm missing: upload public/build from CI"; fi

echo "==> Database + caches"
php artisan migrate --force
php artisan storage:link 2>/dev/null || true
php artisan filament:upgrade
php artisan optimize
php artisan queue:restart

echo "==> Preflight"
php artisan travelorio:preflight --strict
echo "Deployed $(git rev-parse --short HEAD)"
