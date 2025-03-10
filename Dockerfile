FROM php:7.4-fpm-alpine

# Set the working directory inside the container
WORKDIR /var/www/html

# Install required dependencies for PDO, MySQL, and zip
RUN apk add --no-cache --update \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the project files into the container
COPY . /var/www/html

# Run composer install
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# Set user to root (if needed)
USER root

# Copy the entrypoint script and set it as executable
COPY ./php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set the entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose the port for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
