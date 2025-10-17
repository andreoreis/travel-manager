FROM php:8.2-fpm

# Instala dependências básicas
RUN apt-get update && apt-get install -y \
    git zip unzip libzip-dev libonig-dev \
    curl libcurl4-openssl-dev \
    && docker-php-ext-install pdo_mysql mbstring zip curl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD ["php-fpm"]
