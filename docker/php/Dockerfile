FROM php:8.4-fpm-alpine

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
