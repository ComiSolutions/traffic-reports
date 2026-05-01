#!/usr/bin/env bash
set -e

PORT="${PORT:-10000}"

if [ -z "${APP_KEY}" ] || ! echo "${APP_KEY}" | grep -q '^base64:'; then
    export APP_KEY="$(php -r 'echo "base64:".base64_encode(random_bytes(32));')"
fi

if [ -z "${APP_URL}" ] && [ -n "${RENDER_EXTERNAL_HOSTNAME}" ]; then
    export APP_URL="https://${RENDER_EXTERNAL_HOSTNAME}"
fi

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/\${PORT}/${PORT}/g" /etc/apache2/sites-available/000-default.conf

php artisan storage:link || true
php artisan package:discover --ansi
php artisan migrate --force
php artisan config:cache
php artisan view:cache

exec apache2-foreground
