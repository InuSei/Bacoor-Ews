# Use the official PHP Apache image
FROM php:8.2-apache

# Install database drivers
RUN apt-get update && apt-get install -y libpq-dev unzip
RUN docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy all your Laravel files into the server
COPY . .

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