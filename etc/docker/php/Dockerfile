FROM php:7.1-fpm

RUN apt-get update \
    && apt-get install -y libssl-dev libicu-dev g++ zlib1g-dev libmcrypt-dev libpng12-dev libfreetype6-dev \
        libjpeg62-turbo-dev libcurl4-openssl-dev libmagickwand-dev git wget curl libpq-dev \
        software-properties-common python-software-properties \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) zip bcmath sockets mbstring intl pdo pdo_pgsql pgsql json opcache iconv mcrypt gd curl \
    && pecl install xdebug && pecl install apcu\
    && docker-php-ext-enable xdebug apcu

WORKDIR /var/www/eltoken

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer

COPY php-fpm-fcgi.ini $PHP_INI_DIR/php-fpm-fcgi.ini
COPY ./php.ini /usr/local/etc/php/conf.d/

COPY ./usermod.sh /usr/local/bin/usermod.sh
ENTRYPOINT ["/usr/local/bin/usermod.sh"]
