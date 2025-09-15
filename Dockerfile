# PHP DEV
FROM php:8.3-fpm AS dev

RUN apt update -y && \
    apt install -y git \
      libzip-dev && \
    docker-php-ext-install zip \
      pdo_mysql

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

WORKDIR /opt/app

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

CMD ["php-fpm"]
