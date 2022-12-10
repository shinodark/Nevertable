FROM php:5.6-apache

ARG extra_extensions

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions mysqli gd $extra_extensions

COPY classes/ functions/ includes/ libs/ lang/ /var/www/html/
COPY public/*.php public/admin/ public/ajax/ public/images/ public/js/ public/smilies/ public/themes/ /var/www/html/public/

# TODO: data volumes:
# ./cache (cache_dir)
# ./tmp (tmp_dir)
# ./public/avatars (avatar_dir)
# ./public/replays (replay_dir)
# ./public/images/levelshots (TODO: levelsets should be managed from the frontend)

RUN mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini"
COPY docker/vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY install/nevertable_db_schemas.sql /docker-entrypoint-initdb.d/nevertable.sql