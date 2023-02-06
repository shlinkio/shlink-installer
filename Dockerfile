FROM composer:2

RUN apk add --no-cache --virtual .phpize-deps ${PHPIZE_DEPS} && \
    pecl install pcov && \
    docker-php-ext-enable pcov && \
    apk del .phpize-deps
