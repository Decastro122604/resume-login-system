# Use the official PHP image with Apache
FROM php:8.2-apache

# Install necessary PHP extensions for Laravel and PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Copy all project files into the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose the Render port
EXPOSE 10000

# Start the PHP server and point to the public folder
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
