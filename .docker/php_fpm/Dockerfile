FROM php:7.4-fpm

ENV PATH="${PATH}:/root/.composer/vendor/bin"
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y libmcrypt-dev \
    default-mysql-client libmagickwand-dev --no-install-recommends \
    curl wget git libzip-dev zip unzip rsync bash pv mc \
    && docker-php-ext-install pdo_mysql zip gd pcntl

RUN pecl install imagick \
        && docker-php-ext-enable imagick

RUN pecl install mcrypt-1.0.3 \
    && docker-php-ext-enable mcrypt

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /root/.composer/cache/*

#/usr/local/etc/php_fpm/php_fpm.ini-development
#/usr/local/etc/php_fpm/php_fpm.ini-production

# PHP (FPM)
RUN sed -i 's|;date.timezone =|date.timezone = "Europe/Minsk"|g' /usr/local/etc/php/php.ini-development
RUN sed -i 's|memory_limit = 128M|memory_limit = 256M|g' /usr/local/etc/php/php.ini-development

# ADD SYM LINK TO INI
RUN ln -s /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Clear
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
