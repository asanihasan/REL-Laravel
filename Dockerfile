FROM php:8.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite for Laravel URLs
RUN a2enmod rewrite

# Configure Apache Document Root to point to Laravel's public folder
ENV APACHE_DOCUMENT_ROOT /var/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 1. Copy only composer files first (better caching)
COPY composer.json composer.lock ./

# 2. Install dependencies (Production mode)
RUN composer install --no-dev --no-scripts --no-autoloader

# 3. Copy the rest of the application
COPY . .

# 4. Generate optimized autoload files
RUN composer dump-autoload --optimize

# 5. Fix permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Apache exposes Port 80 by default
EXPOSE 80