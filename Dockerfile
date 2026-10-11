# Archer Monitor — PHP 8.1-FPM untuk Laravel 10
FROM php:8.1-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1

# Dependensi sistem + ekstensi PHP yang dibutuhkan Laravel & PhpSpreadsheet
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip zip libzip-dev libonig-dev libicu-dev libxml2-dev \
        libpng-dev libjpeg-dev libfreetype6-dev libwebp-dev default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Konfigurasi PHP (ukuran upload untuk foto & impor Excel)
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-archer.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
