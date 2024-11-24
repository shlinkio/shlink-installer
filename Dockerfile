FROM composer:2

RUN apk add --update linux-headers && \
    apk add --no-cache --virtual .phpize-deps ${PHPIZE_DEPS} && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    apk del .phpize-deps
