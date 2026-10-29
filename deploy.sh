#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR=/var/www/focimor
BRANCH=main
APP_USER=ubuntu

exec 9>/run/lock/focimor-deploy.lock
flock -n 9 || { echo 'Deploy already running.' >&2; exit 1; }

as_app() {
    if [ "$(id -un)" = "$APP_USER" ]; then
        "$@"
    else
        sudo -u "$APP_USER" -H "$@"
    fi
}

cd "$APP_DIR"

echo '[deploy] syncing code'
as_app git fetch --prune origin "$BRANCH"
as_app git reset --hard "origin/$BRANCH"
as_app git clean -fd -e deploy.sh -e backend/.env -e 'backend/.env.*'
as_app git pull --ff-only origin "$BRANCH"

cd backend

echo '[deploy] installing PHP dependencies'
as_app composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo '[deploy] updating database'
as_app php artisan migrate --force
as_app php artisan db:seed --force

echo '[deploy] clearing Laravel cache'
as_app php artisan optimize:clear

echo '[deploy] fixing Laravel writable directories'
sudo chown -R "$APP_USER":www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} +
sudo find storage bootstrap/cache -type f -exec chmod 664 {} +

echo '[deploy] restarting PHP-FPM'
sudo systemctl restart php8.4-fpm
sudo systemctl is-active --quiet php8.4-fpm

echo '[deploy] restarting workers'
sudo systemctl restart focimor-horizon focimor-scheduler
sudo systemctl is-active --quiet focimor-horizon
sudo systemctl is-active --quiet focimor-scheduler

echo '[deploy] completed'
