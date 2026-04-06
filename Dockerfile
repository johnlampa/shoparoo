FROM php:8.2-cli
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql
COPY . /app
WORKDIR /app
RUN composer install --optimize-autoloader --no-scripts --no-interaction
CMD php artisan serve --host=0.0.0.0 --port=$PORT