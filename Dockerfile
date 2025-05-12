# 1. Base PHP image
FROM php:8.2-cli

# 2. System deps for Laravel
RUN apt-get update \
 && apt-get install -y unzip git libzip-dev libonig-dev libpq-dev libxml2-dev \
 && docker-php-ext-install pdo_mysql zip mbstring xml

# 3. Install Composer
RUN curl -sSL https://getcomposer.org/installer | php \
 && mv composer.phar /usr/local/bin/composer

# 4. Set working dir
WORKDIR /app

# 5. Copy everything (including artisan!) into the container
COPY . .

# 6. Install PHP dependencies now that artisan is present
RUN composer install --no-dev --optimize-autoloader

# 7. Expose port (for clarity; Railway injects $PORT at runtime)
EXPOSE 8080

# 8. Launch the built-in server on Railway’s port
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
