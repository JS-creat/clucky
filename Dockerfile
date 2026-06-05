# Estructura multi-etapa: Compilación de Assets con Node
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Etapa final: Servidor Apache con PHP 8.2
FROM php:8.2-apache

# Cambiar repositorios a espejos globales estables para evitar el error 403
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list \
    && sed -i 's/security.debian.org/archive.debian.org/g' /etc/apt/sources.list \
    && sed -i '/stretch-updates/d' /etc/apt/sources.list || true

# Instalar dependencias del sistema esenciales para Laravel
RUN apt-get update -y || true \
    && apt-get install -y --allow-unauthenticated \
        git \
        unzip \
        zip \
        libzip-dev \
        curl \
    && docker-php-ext-install pdo pdo_mysql zip

# Habilitar el módulo rewrite de Apache (Obligatorio para Laravel)
RUN a2enmod rewrite

# Cambiar la raíz de Apache para que apunte a la carpeta /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configurar el directorio de trabajo
WORKDIR /var/www/html

# Copiar el proyecto desde el repositorio
COPY . .

# Copiar los assets ya compilados desde la etapa de Node
COPY --from=node-builder /app/public/build ./public/build

# Instalar Composer de forma global y ejecutar dependencias de PHP
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN env COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader

# Crear directorios clave y asegurar permisos correctos (Evita Error 500)
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Exponer el puerto de Apache
EXPOSE 80

# Comando para iniciar Apache en primer plano
CMD ["apache2-foreground"]