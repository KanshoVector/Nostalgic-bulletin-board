FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html

COPY . /var/www/html/

RUN mkdir -p uploads \
    && chown -R www-data:www-data uploads

EXPOSE 80
