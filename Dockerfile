# 1. Base image
FROM php:8.2-cli

# 2. OS deps for Laravel
RUN apt-get update && apt-get install -y \
    unzip git libzip-dev libonig-dev libpq-dev libxml2-dev \
  && docker-php-ext-install pdo_mysql zip mbstring xml

# 3. Install Composer
RUN curl -sSL https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# 4. Set working dir
WORKDIR /app

# 5. Copy all your code (including artisan, routes/, app/, etc.)
COPY . .

# 6. Now install dependencies (artisan is present, so package:discover will succeed)
RUN composer install --no-dev --optimize-autoloader

# 7. Expose (optional)
EXPOSE 8080

# 8. Start server binding to Railway’s $PORT (fallback to 8080 locally)
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
