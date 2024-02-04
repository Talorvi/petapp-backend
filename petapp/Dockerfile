## Dockerfile
#FROM php:8.2-fpm
#
## Install system dependencies and PHP extensions
#RUN apt-get update && apt-get install -y \
#    libpng-dev \
#    libonig-dev \
#    libxml2-dev \
#    zip \
#    unzip \
#    git \
#    curl \
#    && pecl install redis \
#    && docker-php-ext-enable redis \
#    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
#
## Clear cache
#RUN apt-get clean && rm -rf /var/lib/apt/lists/*
#
## Install PHP extensions
#RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
#
## Get latest Composer
#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#
## Set working directory
#WORKDIR /var/www
#
## Set permissions for the working directory
#RUN chown -R www-data:www-data /var/www
#
#RUN chown -R www:data:www-data /
#
## Expose port 9000 for PHP-FPM
#EXPOSE 9000
#
## Start php-fpm server
#CMD ["php-fpm"]
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Set permissions for the working directory
RUN chown -R www-data:www-data /var/www

RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
