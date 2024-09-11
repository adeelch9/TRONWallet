FROM php:8.3-apache

# Install dependencies
RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    zip

# Enable mod_rewrite
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy the application code
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install GMP extension
RUN apt-get update && apt-get install -y libgmp-dev && \
    docker-php-ext-install gmp

# Install BCMath extension
RUN docker-php-ext-install bcmath

# Install project dependencies
RUN composer install

# Copy .env.example to .env
RUN cp .env.example .env

# Create database and set permissions
RUN touch database/database.sqlite && \
    chmod 666 database/database.sqlite && \
    chown www-data:www-data database/database.sqlite

# Set permissions for database directory
RUN chmod 775 database && \
    chown www-data:www-data database

# Run migrations
RUN php artisan migrate

# Generate application key
RUN php artisan key:generate

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
