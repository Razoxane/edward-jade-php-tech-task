#
# STAGE 1: composer
# Install the composer packages for Symfony, and PHPUnit Bridge, as that
# requires an additional `composer install` step due to potential conflicts
#
FROM composer:1.10.19 as composer

COPY . /app

RUN set -xe \
 && composer config -g repo.packagist composer https://packagist.org \
 && composer install --no-scripts --no-suggest --no-interaction --prefer-dist --optimize-autoloader

RUN composer dump-autoload --optimize --classmap-authoritative

WORKDIR /app
RUN ./bin/phpunit install

#
# STAGE 2: php
# Configure the PHP installation, and copy the require files into place from the
# composer stage of the build.
#
FROM php:7.3-fpm-alpine3.8

RUN apk update \
 && apk add --no-cache $PHPIZE_DEPS \
    bash \
    git \
    zip \
    unzip

RUN rm -rf /var/cache/apk/*

COPY docker/php/php.ini /usr/local/etc/php/conf.d/php.override.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /app
COPY . /app

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY --from=composer /app/vendor /app/vendor
COPY --from=composer /app/bin /app/bin

RUN php bin/console cache:warmup
RUN chown -R www-data:www-data /app/var
RUN chmod -R 0777 "$app_dir/var/"

CMD ["php-fpm", "--nodaemonize"]
