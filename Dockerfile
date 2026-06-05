# Etapa 1: Compilación de assets con Node
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Etapa 2: Servidor Apache con PHP 8.2
FROM php:8.2-apache-bookworm

RUN apt-get update && apt-get install -y \
        git unzip zip libzip-dev curl \
    && docker-php-ext-install pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# --- OPTIMIZACIÓN DE CACHÉ PARA COMPOSER ---
# Copiamos solo los archivos de composer primero
COPY composer.json composer.lock ./
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN env COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --no-scripts --no-autoloader
# --------------------------------------------

# Ahora sí, copiamos el resto del código del proyecto
COPY . .
COPY --from=node-builder /app/public/build ./public/build

# Terminamos de optimizar composer con el código ya dentro
RUN env COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader

RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
