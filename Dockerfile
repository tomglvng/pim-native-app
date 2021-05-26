FROM php:8-cli AS composer
ENV COMPOSER_HOME=/tmp
RUN apt-get update && \
    apt-get install -y \
        git \
        unzip \
        curl
COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer
COPY ./docker/php/composer.ini /usr/local/etc/php/conf.d/custom.ini
ENTRYPOINT ["composer"]
CMD ["/bin/true"]

FROM composer AS vendors
ARG BUILD_ENV=prod
WORKDIR /srv/app
RUN mkdir /srv/app/vendor
COPY composer.json composer.lock symfony.lock ./
RUN if [ "$BUILD_ENV" = "prod" ]; then \
    composer install \
        --no-scripts \
        --no-interaction \
        --no-ansi \
        --prefer-dist \
        --optimize-autoloader \
        --no-dev \
    ; fi

FROM php:8-fpm AS fpm
ARG BUILD_ENV=prod
ARG USER=www-data
RUN apt-get update && \
    apt-get install -y \
        libicu-dev \
        libonig-dev \
        libpq-dev && \
    docker-php-ext-install \
        bcmath \
        intl \
        pdo_pgsql
RUN if [ "$BUILD_ENV" = "dev" ]; then \
    pecl install \
        xdebug && \
    docker-php-ext-enable \
        xdebug \
    ; fi
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
COPY ./docker/php/fpm.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./docker/php/fpm.conf /usr/local/etc/php-fpm.d/docker.conf
RUN mkdir /srv/app && chown $USER /srv/app
USER $USER
WORKDIR /srv/app
COPY . .
COPY --from=vendors /srv/app/vendor vendor

FROM php:8-cli AS php
ARG BUILD_ENV=prod
ARG USER=www-data
RUN apt-get update && \
    apt-get install -y \
        libicu-dev \
        libonig-dev \
        libpq-dev && \
    docker-php-ext-install \
        bcmath \
        intl \
        pdo_pgsql
RUN if [ "$BUILD_ENV" = "dev" ]; then \
    pecl install \
        xdebug && \
    docker-php-ext-enable \
        xdebug \
    ; fi
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
RUN mkdir /srv/app && chown $USER /srv/app
USER $USER
WORKDIR /srv/app
COPY . .
COPY --from=vendors /srv/app/vendor vendor
