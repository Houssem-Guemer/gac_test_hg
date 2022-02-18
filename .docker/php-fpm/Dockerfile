FROM php:7.4.11-fpm-alpine

# Not using eu.gcr.io/hubside-infra-prod/team-infra/docker-images/php-fpm:7.2-stretch
# as it presumes Symfony is already installed, and fails to start if it's not.

WORKDIR /application

# Install PHP modules
RUN apk add --no-cache --virtual \
        .phpize-deps $PHPIZE_DEPS \
        autoconf \
        pkgconf \
        libssl1.1 \
        openssl-dev \
        freetype-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        libmcrypt-dev \
        git \
        openssh-client \
        curl \
        wget \
        libtool \
        zlib-dev \
        icu-dev \
        g++ \
        linux-headers \
    && pecl bundle -d /usr/src/php/ext redis \
    && docker-php-ext-install -j2 redis \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j2 intl \
    && pecl bundle -d /usr/src/php/ext mcrypt \
    && docker-php-ext-configure mcrypt \
    && pecl bundle -d /usr/src/php/ext apcu \
    && docker-php-ext-install -j2 apcu \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j2 bcmath gd iconv mcrypt \
    && docker-php-ext-enable opcache \
    && rm /usr/src/php/ext/*.tgz \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.profiler_enable = 0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_connect_back=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_port=10000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.max_nesting_level=256" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer --install-dir=/usr/bin \
    && chmod +x /usr/bin/composer \
    && php -r "unlink('composer-setup.php');" \
    && composer --version \
    && pecl channel-update pecl.php.net \
    && composer global require "phpunit/phpunit" \
    && export PATH=$PATH:/root/.composer/vendor/bin \
    && ln -s /root/.composer/vendor/bin/phpunit /usr/bin/phpunit

RUN composer self-update
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony
