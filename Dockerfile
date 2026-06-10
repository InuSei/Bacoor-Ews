# Use the official PHP 8.4 Apache image
FROM php:8.4-apache

# Install system dependencies and MySQL drivers
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy all your Laravel files into the server
COPY . .

# IMPORTANT: Tell Laravel to use sqlite during the build to avoid DB connection errors
ENV DB_CONNECTION=sqlite

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Give the server permission to write logs and caches
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Tell Apache to look inside Laravel's "public" folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN a2enmod rewrite

# Run your database migrations and start the server!
CMD php artisan migrate --force && apache2-foreground