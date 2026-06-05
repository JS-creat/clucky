# Etapa 1: Assets con Node
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Etapa 2: PHP con Alpine
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
        git unzip zip curl libzip-dev nginx bash

RUN docker-php-ext-install pdo pdo_mysql zip

# Nginx config
RUN mkdir -p /run/nginx
RUN echo 'server {
    listen 80;
    root /var/www/html/public;
    index index.php;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}' > /etc/nginx/http.d/default.conf

WORKDIR /var/www/html
COPY . .
COPY --from=node-builder /app/public/build ./public/build
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN env COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader

RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

RUN echo '#!/bin/sh' > /start.sh \
    && echo 'php-fpm -D' >> /start.sh \
    && echo 'nginx -g "daemon off;"' >> /start.sh \
    && chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]