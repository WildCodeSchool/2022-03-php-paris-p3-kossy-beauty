#
# Prep App's PHP Dependencies
#
FROM composer:2.1 as vendor

WORKDIR /app

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --quiet

FROM php:8.0-fpm-alpine as phpserver

# add cli tools
RUN apk update \
    && apk upgrade \
    && apk add nginx

RUN apk add --no-cache \
      libzip-dev \
      zip \
    && docker-php-ext-install zip

# silently install 'docker-php-ext-install' extensions
RUN set -x

RUN docker-php-ext-install pdo_mysql bcmath > /dev/null


COPY nginx.conf /etc/nginx/nginx.conf

COPY php.ini /usr/local/etc/php/conf.d/local.ini
RUN cat /usr/local/etc/php/conf.d/local.ini

WORKDIR /var/www

COPY . /var/www/
COPY --from=vendor /app/vendor /var/www/vendor

#
# Prep App's Frontend CSS & JS now
# so some symfony UX dependencies can access to vendor
#
RUN apk add nodejs
RUN apk add npm
RUN npm install npm@latest -g
RUN npm install yarn@latest -g
RUN node -v
RUN npm -v
RUN yarn install
RUN yarn run build

EXPOSE 80

COPY docker-entry.sh /etc/entrypoint.sh
ENTRYPOINT ["sh", "/etc/entrypoint.sh"]
