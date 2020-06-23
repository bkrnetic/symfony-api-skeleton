FROM php:fpm-alpine

COPY wait-for-it.sh /usr/bin/wait-for-it

RUN chmod +x /usr/bin/wait-for-it

#RUN apk --update --no-cache add git autoconf
#
#RUN docker-php-ext-install pdo_mysql
#
#RUN pecl install xdebug
#
#RUN docker-php-ext-enable xdebug
#
#COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk update \
    && apk add  --no-cache git mysql-client curl libmcrypt libmcrypt-dev openssh-client icu-dev \
    libxml2-dev freetype-dev libpng-dev libjpeg-turbo-dev g++ make autoconf libzip-dev \
    && docker-php-source extract \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-source delete \
    && docker-php-ext-install pdo_mysql soap intl zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /tmp/*

WORKDIR /var/www

CMD composer install ; wait-for-it database:3306 ;  php-fpm

EXPOSE 9000
