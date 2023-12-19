FROM php:5.4-apache
RUN docker-php-ext-install mysql && docker-php-ext-enable mysql
RUN a2enmod rewrite
