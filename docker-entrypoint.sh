#!/bin/sh
set -e

: "${PORT:=8080}"
export PORT

echo "[DEBUG] Running as user: $(whoami)"
echo "[DEBUG] PORT=$PORT"
echo "[DEBUG] /run/php exists: $(ls -la /run/php 2>/dev/null || echo 'MISSING')"
echo "[DEBUG] php-fpm8.2 binary: $(which php-fpm8.2 2>/dev/null || echo 'NOT FOUND')"
echo "[DEBUG] nginx binary: $(which nginx 2>/dev/null || echo 'NOT FOUND')"

envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf
echo "[DEBUG] Nginx config written"

if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "[ERROR] Composer dependencies are missing." >&2
    exit 1
fi

mkdir -p /run/php /var/log/php-fpm /var/lib/php/sessions/todo-list-php
chown -R www-data:www-data /run/php /var/log/php-fpm /var/lib/php/sessions/todo-list-php
echo "[DEBUG] Directories ready"

echo "[DEBUG] Starting PHP-FPM..."
php-fpm8.2 --daemonize --fpm-config /etc/php/8.2/fpm/pool.d/todo-list-php.conf
sleep 2

if ! pgrep -x php-fpm8.2 > /dev/null 2>&1; then
    echo "[ERROR] PHP-FPM failed to start." >&2
    exit 1
fi

echo "[OK] PHP-FPM started."

echo "[DEBUG] Testing nginx config..."
nginx -t

echo "[DEBUG] Starting Nginx..."
exec nginx -g "daemon off;"