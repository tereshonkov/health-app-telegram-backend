FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libmariadb-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath sockets intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ARG WWWUSER=1000
RUN useradd -m -u ${WWWUSER} -s /bin/bash devuser

WORKDIR /var/www

COPY composer.json composer.lock* ./

RUN composer install --no-scripts --no-autoloader --no-interaction

COPY . .

RUN chown -R devuser:devuser /var/www/storage /var/www/bootstrap/cache

USER devuser

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]