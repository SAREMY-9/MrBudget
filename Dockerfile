# Start from PHP 8.2 FPM
FROM php:8.2-fpm

# Install system dependencies, Node.js, npm, SQLite
RUN apt-get update && apt-get install -y \
    git zip unzip curl nodejs npm sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite mbstring bcmath

# Copy composer from official composer image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Create SQLite database if it doesn't exist
RUN mkdir -p storage && touch storage/database.sqlite

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend assets with Vite
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose Laravel server port
EXPOSE 8000

# Environment placeholders (optional)
ENV APP_ENV=production
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/var/www/html/storage/database.sqlite

# Run migrations then start Laravel dev server
CMD php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=8000
