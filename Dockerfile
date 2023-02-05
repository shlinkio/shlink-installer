FROM composer:2

RUN apk add --no-cache --virtual .phpize-deps ${PHPIZE_DEPS} unixodbc-dev && \
    pecl install pcov && \
    docker-php-ext-enable pcov && \
    apk del .phpize-deps
