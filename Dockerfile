FROM php:8.2-fpm AS base

RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        gettext-base \
        libpq-dev \
        unzip \
    && docker-php-ext-install pdo pdo_pgsql opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

FROM base AS composer

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json ./

RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --optimize-autoloader

COPY . .

RUN composer dump-autoload --optimize --no-dev

FROM base AS production

WORKDIR /var/www/html

COPY --from=composer /var/www/html /var/www/html

RUN mkdir -p /var/log/php-fpm /var/lib/php/sessions/todo-list-php \
    && chown -R www-data:www-data \
        /var/www/html \
        /var/log/php-fpm \
        /var/lib/php/sessions/todo-list-php \
    && chown -R www-data:www-data /var/log/nginx

RUN mkdir -p /run/php \
    && chown -R www-data:www-data /run/php

COPY docker/php-fpm.conf /etc/php/8.2/fpm/pool.d/todo-list-php.conf

RUN rm -f /etc/php/8.2/fpm/pool.d/www.conf

COPY docker/nginx.conf /etc/nginx/conf.d/default.conf.template

RUN rm -f /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/conf.d/default.conf

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:${PORT:-8080}/ || exit 1

ENTRYPOINT ["docker-entrypoint.sh"]