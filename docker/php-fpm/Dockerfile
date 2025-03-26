FROM php:8.3-fpm

WORKDIR '/app'

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libcurl4-openssl-dev \
    && docker-php-ext-install zip pdo pdo_mysql curl \
