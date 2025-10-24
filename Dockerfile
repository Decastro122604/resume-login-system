# Use the official PHP 8.2 image with Apache
FROM php:8.2-apache

# Install system dependencies including PostgreSQL dev libraries
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev curl \
    && docker-php-ext-install pdo pdo_pgsql

# Enable Apache rewrite module (needed for Laravel routes)
RUN a2enmod rewrite

# Copy the Laravel project files into the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set correct permissions for Laravel storage and cache
RUN chmod -R 775 storage bootstrap/cache

# Expose port 80 for Render
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
