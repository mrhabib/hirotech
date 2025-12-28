FROM php:8.4-fpm-alpine
RUN apk add --no-cache nodejs npm curl zip unzip libpng-dev libxml2-dev oniguruma-dev
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd xml
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN npm install -g npm@latest
WORKDIR /var/www
USER www-data
