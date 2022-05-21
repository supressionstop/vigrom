FROM php:8.1.6-fpm-alpine3.15

### php extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
        install-php-extensions intl apcu opcache pdo pdo_pgsql pgsql
### php extensions


WORKDIR /var/www/app
