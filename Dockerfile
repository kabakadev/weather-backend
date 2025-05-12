# 1. Base image with PHP CLI and extensions
FROM php:8.2-cli

# 2. Install OS-level deps (for MySQL, Git, unzip, etc.)
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libonig-dev \
    libpq-dev \
    libxml2-dev \
  && docker-php-ext-install pdo_mysql zip mbstring xml

# 3. Install Composer
RUN curl -sSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 4. Set working directory
WORKDIR /app

# 5. Copy only composer files, install deps (layer cache)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# 6. Copy the rest of the code
COPY . .

# 7. Generate APP_KEY if you want, or set it via Railway env var
#    (you can skip this if you set APP_KEY in Railway directly)
# RUN php artisan key:generate

# 8. Expose the port (optional, for clarity)
EXPOSE 8080

# 9. Start the server on Railway’s dynamic port
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
