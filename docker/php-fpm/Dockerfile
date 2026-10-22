FROM php:8.4.27-fpm

WORKDIR "/application"

ARG DOCKER_GID=1000
ARG DOCKER_UID=1000

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# установка необходимых пакетов
RUN apt-get update \
    && apt-get install -y \
        zlib1g-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libzip-dev \
        libpng-dev \
        libpq-dev \
        libldb-dev  \
        libldap2-dev \
        librdkafka-dev \
        git \
        vim \
        # установка php расширений \
        && docker-php-ext-install -j$(nproc) bcmath zip pdo_pgsql exif sockets ldap pcntl \
        && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN pecl install redis \
    && docker-php-ext-enable redis.so

RUN pecl install pcov \
    && docker-php-ext-enable pcov

RUN pecl install rdkafka && docker-php-ext-enable rdkafka

# установка composer
COPY --from=composer/composer:2-bin /composer /usr/bin/composer

# настройка пользователя и прав
RUN groupadd -g $DOCKER_GID dev
RUN useradd -g $DOCKER_GID -u $DOCKER_UID dev
RUN mkdir /home/dev
RUN chown -R dev:dev /home/dev

# копирование файлов проекта
COPY ./App /application

COPY "docker/php-fpm/php-ini-overrides.ini" "/usr/local/etc/php/conf.d/zz-overrides.ini"

# Для Тинкера
RUN mkdir -p /var/www/.config/psysh
RUN chown 33:33 /var/www/.config/psysh
RUN chown 33:33 /application/bootstrap/cache

RUN composer install

USER dev