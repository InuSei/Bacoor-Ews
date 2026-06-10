FROM php:8.4-apache

# Install system dependencies and BOTH MySQL and PostgreSQL drivers
# We install libpq-dev for Postgres, and then explicitly install the extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql pdo_pgsql gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Dummy file to satisfy Laravel build-time checks
RUN mkdir -p database && touch database/database.sqlite
ENV DB_CONNECTION=sqlite

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Setup Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN a2enmod rewrite

# Final step: Run migrations using the real environment variables
CMD php artisan migrate --force && apache2-foreground