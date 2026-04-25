FROM php:8.4-apache

WORKDIR /var/www/html

# Install system dependencies + required PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev zip libonig-dev \
    && docker-php-ext-install \
    pdo pdo_pgsql zip mbstring bcmath

# Enable Apache rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project
COPY . .

# Install dependencies
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader

# Fix permissions (IMPORTANT FIX)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Ensure Laravel log file exists
RUN touch storage/logs/laravel.log

# Point Apache to Laravel public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf

# IMPORTANT: Render listens on 10000, but Apache must still serve correctly
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf || true
RUN sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-available/*.conf || true

EXPOSE 10000

CMD ["apache2-foreground"]