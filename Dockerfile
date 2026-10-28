FROM composer:2.8 AS vendor

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --no-dev --optimize


FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json ./
RUN npm install --ignore-scripts

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build


FROM php:8.3-fpm-bookworm AS app

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev \
    && docker-php-ext-install -j"$(nproc)" bcmath pdo_mysql zip opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor --chown=www-data:www-data /var/www/html ./
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build
COPY docker/php/zz-production.ini /usr/local/etc/php/conf.d/zz-production.ini
COPY docker/php/entrypoint.sh /usr/local/bin/app-entrypoint

RUN chmod +x /usr/local/bin/app-entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data

ENTRYPOINT ["app-entrypoint"]
CMD ["php-fpm"]
