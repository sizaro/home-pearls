# Use official PHP 8.4 image with Apache
FROM php:8.4-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Ensure .env exists (copy from example if missing)
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Update .env for dummy database so Laravel boots
RUN sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env \
    && sed -i "s/DB_HOST=.*/DB_HOST=127.0.0.1/" .env \
    && sed -i "s/DB_PORT=.*/DB_PORT=5432/" .env \
    && sed -i "s/DB_DATABASE=.*/DB_DATABASE=dummy/" .env \
    && sed -i "s/DB_USERNAME=.*/DB_USERNAME=dummy/" .env \
    && sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=dummy/" .env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate application key
RUN php artisan key:generate

# Clear caches
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:clear \
    && php artisan view:clear

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Set Apache DocumentRoot to Laravel public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]