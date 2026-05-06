FROM php:8.4-apache

WORKDIR /var/www/html

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev zip libonig-dev \
    && docker-php-ext-install pdo pdo_pgsql zip mbstring bcmath

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install PHP dependencies
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader

# Install Node.js (Vite requires Node 20+)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Clean install to avoid npm bugs
RUN rm -rf node_modules package-lock.json \
    && npm install

# Build frontend
RUN npm run build

# Create storage log file + fix permissions
RUN mkdir -p storage/logs \
    && touch storage/logs/laravel.log \
    && chmod -R 777 storage bootstrap/cache

# Set Apache document root to Laravel public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf

# -------------------------------
# FIXED: Apache Virtual Host (SAFE METHOD)
# -------------------------------
RUN printf '<VirtualHost *:10000>\n\
    DocumentRoot /var/www/html/public\n\
\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

# Change Apache port from 80 → 10000 (Render requirement)
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf

EXPOSE 10000

CMD php artisan migrate --force || true && apache2-foreground