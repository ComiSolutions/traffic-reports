#!/usr/bin/env bash
set -e

PORT="${PORT:-10000}"

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/\${PORT}/${PORT}/g" /etc/apache2/sites-available/000-default.conf

php artisan storage:link || true
php artisan package:discover --ansi
php artisan migrate --force
php artisan config:cache
php artisan view:cache

exec apache2-foreground
