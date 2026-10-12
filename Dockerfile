FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative --no-interaction \
    && php artisan package:discover --ansi

FROM node:20-bookworm-slim AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY --from=vendor /app/vendor ./vendor
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build

FROM php:8.4-apache-bookworm AS runtime

WORKDIR /var/www/html
RUN apt-get update \
    && apt-get install -y --no-install-recommends curl gettext-base libonig-dev libpq-dev libzip-dev unzip \
    && docker-php-ext-install pdo_pgsql zip mbstring bcmath \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY docker/apache-vhost.conf.template /etc/apache2/sites-available/000-default.conf.template
COPY docker/ports.conf.template /etc/apache2/ports.conf.template
COPY docker/entrypoint.sh /usr/local/bin/home-pearls-entrypoint

RUN chmod +x /usr/local/bin/home-pearls-entrypoint \
    && chown -R www-data:www-data storage bootstrap/cache \
    && find storage bootstrap/cache -type d -exec chmod 775 {} \; \
    && find storage bootstrap/cache -type f -exec chmod 664 {} \;

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    PORT=10000

EXPOSE 10000
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 CMD curl --fail "http://127.0.0.1:${PORT}/up" || exit 1
ENTRYPOINT ["/usr/local/bin/home-pearls-entrypoint"]
