#!/usr/bin/env bash
# Backend deploy: run on the server (CI runs it over SSH as the "deploy" user).
#
#   bash /var/www/fayerahammed/deploy/deploy-backend.sh
#
# Environment overrides: APP_DIR (repo root), BRANCH, PHP_FPM_SERVICE.

# Everything is wrapped in main() and called on the last line. `git pull` rewrites this very
# file, and bash reads scripts incrementally, so a half-read file could otherwise misbehave.
main() {
  set -euo pipefail
  umask 002 # files stay group-writable so www-data (PHP-FPM, queue worker) can use them

  local APP_DIR="${APP_DIR:-/var/www/fayerahammed}"
  local BRANCH="${BRANCH:-main}"
  local PHP_FPM_SERVICE="${PHP_FPM_SERVICE:-php8.3-fpm}"

  step() { printf '\n==> %s\n' "$*"; }

  # One deploy at a time.
  exec 9>/tmp/fayer-deploy.lock
  flock -n 9 || { echo "Another deploy is already running." >&2; exit 1; }

  step "Pulling ${BRANCH}"
  cd "$APP_DIR"
  git pull --ff-only origin "$BRANCH"

  cd "$APP_DIR/backend"

  step "Installing PHP dependencies (no dev, optimized autoloader)"
  composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

  step "Running migrations"
  php artisan migrate --force

  step "Linking public storage (once)"
  [ -L public/storage ] || php artisan storage:link

  step "Rebuilding caches"
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan event:cache
  php artisan filament:optimize

  step "Clearing the cached home payload"
  php artisan cache:forget home || true

  step "Restarting queue workers"
  php artisan queue:restart

  step "Reloading PHP-FPM (refreshes OPcache)"
  sudo -n systemctl reload "$PHP_FPM_SERVICE"

  step "Deploy finished: $(git -C "$APP_DIR" rev-parse --short HEAD)"
}

main "$@"
exit $?
