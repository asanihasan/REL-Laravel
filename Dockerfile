FROM php:8.4-apache

# 1. Install system dependencies
RUN rm -rf /var/lib/apt/lists/* && \
    apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip

# 3. Enable Apache mod_rewrite
RUN a2enmod rewrite

# 4. Configure Apache Document Root
ENV APACHE_DOCUMENT_ROOT /var/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 5. NEW: Explicitly allow .htaccess overrides for Laravel
RUN echo '<Directory /var/www/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# 6. Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 7. Copy only composer files first
COPY composer.json composer.lock ./

# 8. Install dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

# 9. Copy application files
COPY . .

# 10. Dump Autoload
RUN composer dump-autoload --optimize

# 11. Fix permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80