## FROM php:8.2-apache

# # Enable Apache mod_rewrite
# RUN a2enmod rewrite

# # Copy project files into container
# COPY . /var/www/html/

# # Set correct permissions
# RUN chown -R www-data:www-data /var/www/html

# # Expose port 80
# EXPOSE 80


FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    git \
    libzip-dev \
    && docker-php-ext-install zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (better caching 🔥)
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy remaining project files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Apache config (important for routing)
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
</Directory>' >> /etc/apache2/apache2.conf

# Expose port
EXPOSE 80