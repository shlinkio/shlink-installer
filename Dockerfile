FROM composer:2

COPY --from=ghcr.io/php/pie:bin /pie /usr/bin/pie
RUN apk add --update linux-headers && \
    apk add --no-cache --virtual .phpize-deps ${PHPIZE_DEPS} && \
    pie install xdebug/xdebug && \
    apk del .phpize-deps
