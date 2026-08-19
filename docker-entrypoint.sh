#!/bin/sh
set -e

: "${PORT:=8080}"
export PORT

envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "[ERROR] Composer dependencies are missing." >&2
    exit 1
fi

mkdir -p /run/php /var/log/php-fpm /var/lib/php/sessions/todo-list-php
chown -R www-data:www-data /run/php /var/log/php-fpm /var/lib/php/sessions/todo-list-php

php-fpm --daemonize --fpm-config /etc/php/8.2/fpm/pool.d/todo-list-php.conf 2>&1 || {
    echo "[ERROR] PHP-FPM failed to start." >&2
    exit 1
}
sleep 2

if [ ! -S /run/php/php-fpm.sock ]; then
    echo "[ERROR] PHP-FPM socket not found at /run/php/php-fpm.sock" >&2
    ls -la /run/php/ >&2
    cat /var/log/php-fpm/todo-list-php.error.log 2>/dev/null || echo "(log empty or missing)"
    exit 1
fi

echo "[OK] PHP-FPM started."
exec nginx -g "daemon off;"