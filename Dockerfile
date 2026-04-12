# --- Stage 1: Build Frontend Assets ---
FROM node:20-alpine AS assets
WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build


# --- Stage 2: PHP + Laravel ---
FROM php:8.2-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip unzip git curl oniguruma-dev postgresql-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring zip exif pcntl gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Copy built frontend assets
COPY --from=assets /app/public/build ./public/build

# Install Laravel dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Fix permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Copy entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]

# Railway uses dynamic port
ENV PORT=8080
EXPOSE 8080

# ✅ IMPORTANT: Use Laravel server (NOT php-fpm)
CMD php artisan serve --host=0.0.0.0 --port=8080
