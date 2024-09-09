FROM php:8.0-cli

RUN apt-get update -y && apt-get install -y openssl zip unzip git
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /app/backend

COPY . .

RUN composer update --no-scripts

EXPOSE 8000

CMD php artisan serve  --host=0.0.0.0