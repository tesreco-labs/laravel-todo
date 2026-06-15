FROM composer:2 AS dependencies

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader \
    --no-scripts \
    --no-dev


FROM dunglas/frankenphp:php8.3

WORKDIR /app

RUN install-php-extensions \
    pdo_mysql \
    mysqli \
    redis \
    opcache \
    intl \
    zip \
    bcmath \
    pcntl

COPY --from=dependencies /app/vendor ./vendor

COPY . .

RUN php artisan package:discover --ansi

RUN mkdir -p storage/logs bootstrap/cache

RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

USER www-data

EXPOSE 80

CMD ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=80"]