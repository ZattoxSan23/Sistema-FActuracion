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
    openssl-dev

# Limpiar caché
RUN apk cache clean

# Instalar extensiones PHP
RUN docker-php-ext-install pdo pdo_pgsql pgsql bcmath intl zip gd xml opcache \
    && pecl install redis \
    && docker-php-ext-enable redis

# Instalar Xdebug (solo dev)
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug || true

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario para evitar permisos de root
RUN addgroup -g ${GID} laravel && \
    adduser -u ${UID} -G laravel -D laravel

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Configurar git para composer
RUN git config --global --add safe.directory /var/www/html

# Cambiar al usuario laravel
USER laravel

EXPOSE 9000

CMD ["php-fpm"]
