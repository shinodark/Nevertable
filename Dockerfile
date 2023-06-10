FROM php:5.6-apache

ARG extra_extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions mysqli gd $extra_extensions
RUN mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini"

COPY docker/vhost.conf /etc/apache2/sites-enabled/000-default.conf

ADD ./app /var/www/html/

VOLUME /public/data