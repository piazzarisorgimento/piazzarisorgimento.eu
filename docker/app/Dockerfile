FROM php:7.4-fpm

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y --no-install-recommends unzip libpng-dev libonig-dev && \
	pecl install xdebug-2.9.2 && \
	docker-php-ext-enable xdebug && \
    docker-php-ext-install pdo pdo_mysql gd mbstring

WORKDIR /var/www
