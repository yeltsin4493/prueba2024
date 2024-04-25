# Usa una imagen base de PHP 8.2.3 con Apache
FROM php:8.2.3-apache

WORKDIR /var/www/html

RUN apt-get update -y && apt-get install -y libmariadb-dev

RUN docker-php-ext-install mysqli