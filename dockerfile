FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable pdo pdo_mysql

RUN apt-get install zip -y

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

#composer install
#RUN docker-php-ext-install @composer # default installed
#RUN docker-php-ext-enable composer
#launch composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY composer.json ./
RUN composer install

RUN a2enmod rewrite \
    && service apache2 restart

COPY . .