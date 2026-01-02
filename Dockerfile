FROM php:8.4-fpm-alpine AS base
RUN apk add --no-cache postgresql-dev && docker-php-ext-install pdo_pgsql
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app

FROM base AS deps
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist

FROM base
COPY --from=deps /app/vendor ./vendor
COPY . .
RUN composer dump-autoload --optimize
RUN php artisan config:cache && php artisan route:cache
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
