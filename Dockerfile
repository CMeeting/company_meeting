FROM php:7.3-cli

MAINTAINER shuwei
RUN apt-get update && apt-get install -y vim libzip-dev zip libpq-dev libpng-dev wget
RUN docker-php-ext-install pgsql pdo_pgsql pcntl gd zip

# Install redis extension
RUN docker-php-source extract
RUN curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/refs/tags/5.3.3.tar.gz
RUN tar -zxvf /tmp/redis.tar.gz -C /usr/src/php/ext && mv /usr/src/php/ext/phpredis-* /usr/src/php/ext/phpredis
RUN docker-php-ext-install phpredis && docker-php-source delete

ENV DIR /php_compdf_server
WORKDIR $DIR

COPY . $DIR
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
RUN composer self-update 1.10.26
RUN composer update

EXPOSE 3011
#RUN chmod +x $DIR/start.sh
#CMD $DIR/start.sh
CMD php artisan serve
