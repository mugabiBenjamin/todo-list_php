#!/bin/sh
set -e

: "${PORT:=8080}"

export PORT

envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "[ERROR] Composer dependencies are missing. Ensure 'composer install' ran during the Docker build." >&2
    exit 1
fi

mkdir -p /var/log/php-fpm /var/lib/php/sessions/todo-list-php
chown -R www-data:www-data /var/log/php-fpm /var/lib/php/sessions/todo-list-php

php-fpm8.2 --daemonize --fpm-config /etc/php/8.2/fpm/pool.d/todo-list-php.conf

if ! pgrep -x php-fpm8.2 > /dev/null 2>&1; then
    echo "[ERROR] PHP-FPM failed to start." >&2
    exit 1
fi

exec nginx -g "daemon off;"