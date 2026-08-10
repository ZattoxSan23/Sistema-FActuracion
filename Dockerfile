FROM php:8.3-fpm-alpine

# Argumentos de construcción
ARG UID=1000
ARG GID=1000

# Instalar dependencias del sistema
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    autoconf \
    g++ \
    make \
    linux-headers \
    postgresql-dev \
    libpq \
    openssl-dev \
    nginx \
    supervisor

# Limpiar caché
RUN apk cache clean

# Instalar extensiones PHP
RUN docker-php-ext-install pdo pdo_pgsql pgsql bcmath intl zip gd xml opcache \
    && pecl install redis \
    && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario
RUN addgroup -g ${GID} laravel && \
    adduser -u ${UID} -G laravel -D laravel

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar TODO el código primero (incluyendo artisan)
COPY --chown=laravel:laravel . /var/www/html

# Crear carpetas necesarias
RUN mkdir -p /var/www/html/storage/framework/cache/data \
                /var/www/html/storage/framework/sessions \
                /var/www/html/storage/framework/views \
                /var/www/html/storage/logs \
                /var/www/html/bootstrap/cache \
                /var/run/nginx \
                /var/log/nginx \
                /var/log/supervisor

# Permisos
RUN chmod +x /var/www/html/docker/entrypoint.sh && \
    chown -R laravel:laravel /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Instalar dependencias PHP (ahora artisan SÍ existe)
USER laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Volver a root para supervisord
USER root

# Copiar configuraciones
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Exponer puerto
EXPOSE 8080

# Script de inicio
ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
