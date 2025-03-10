FROM php:7.4-fpm-alpine

# Set the working directory inside the container
WORKDIR /var/www/html

# Install the required dependencies for PDO and MySQL
RUN apk add --no-cache --update \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy the project files into the container
COPY . /var/www/html

# Run composer install
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist


USER root

COPY ./php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose the port for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
