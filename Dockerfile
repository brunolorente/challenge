FROM php:8.1-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    procps \
    libpq-dev

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd pdo_pgsql pgsql # <- Agregar pdo_pgsql y pgsql

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar la aplicación
COPY . /var/www

# Establecer el directorio de trabajo
WORKDIR /var/www

RUN echo "memory_limit=1G" >> /usr/local/etc/php/php.ini

# Instalar dependencias de Composer
RUN composer install --no-interaction

# Exponer puerto 9000
EXPOSE 9000

# Iniciar PHP-FPM
CMD ["php-fpm"]
