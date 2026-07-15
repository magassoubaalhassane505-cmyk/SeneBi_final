# Stage 1: Build front-end assets with Node
FROM node:20-alpine AS asset-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Main production image with PHP and Apache
FROM php:8.3-apache

# Install required system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd opcache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy custom Apache virtual host configuration
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Set working directory in container
WORKDIR /var/www/html

# Copy all source files
COPY . .

# Copy built front-end assets from the first stage
COPY --from=asset-builder /app/public/build ./public/build

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install production dependencies only, optimize autoloader
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Adjust file and folder permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy startup entrypoint script and set execution rights
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose default port (Render will configure PORT dynamically)
EXPOSE 80

# Use the custom entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Run Apache in the foreground
CMD ["apache2-foreground"]
