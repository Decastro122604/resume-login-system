# Use the official PHP 8.2 with Apache image
FROM php:8.2-apache

# Install required PHP extensions for Laravel + PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev unzip git curl && \
    docker-php-ext-install pdo pdo_pgsql pgsql

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all project files into the container
COPY . .

# Set permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Change Apache DocumentRoot to /var/www/html/public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
