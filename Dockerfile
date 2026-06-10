FROM php:8.4-apache

# Install dependencies
RUN apt-get update && apt-get install -y libpq-dev libpng-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# --- THE FIX: Create a dummy SQLite file so Laravel doesn't crash ---
RUN mkdir -p database && touch database/database.sqlite

# Tell Laravel to use sqlite for the build phase only
ENV DB_CONNECTION=sqlite

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Give permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN a2enmod rewrite

# Run your real migration (this will use your REAL MySQL variables from Render)
CMD php artisan migrate --force && apache2-foreground