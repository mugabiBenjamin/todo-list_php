#!/bin/sh
set -e

: "${PORT:=8080}"
export PORT

envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "[ERROR] Composer dependencies are missing." >&2
    exit 1
fi

# Ensure directories exist and are writable
mkdir -p /run/php /var/log/php-fpm /var/lib/php/sessions/todo-list-php
chown -R www-data:www-data /run/php /var/log/php-fpm /var/lib/php/sessions/todo-list-php

php-fpm8.2 --daemonize --fpm-config /etc/php/8.2/fpm/pool.d/todo-list-php.conf
sleep 2

if ! pgrep -x php-fpm8.2 > /dev/null 2>&1; then
    echo "[ERROR] PHP-FPM failed to start." >&2
    exit 1
fi

echo "[OK] PHP-FPM started successfully."
exec nginx -g "daemon off;"