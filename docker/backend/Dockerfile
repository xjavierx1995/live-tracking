FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        zip \
        curl \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]