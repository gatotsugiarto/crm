FROM php:8.2-fpm

COPY php.ini /usr/local/etc/php/php.ini

# Install ekstensi dan tools
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Install dependency yang diperlukan
RUN apt-get update && apt-get install -y \
    git unzip curl libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install intl pdo_mysql zip gd

# Instal Composer secara global
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer    

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

RUN apt-get update && apt-get install -y \
    libmagickwand-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick
        

WORKDIR /var/www/html
# EXPOSE 9000
