FROM php:8.2-cli
RUN apt-get update && apt-get install -y ca-certificates libpq-dev curl git unzip libzip-dev nodejs npm && docker-php-ext-install pdo pdo_pgsql zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY . /app
WORKDIR /app
RUN composer install --optimize-autoloader --no-scripts --no-interaction
RUN npm ci && npm run build
CMD ["sh", "-lc", "php artisan storage:link --force && php artisan serve --host=0.0.0.0 --port=$PORT"]