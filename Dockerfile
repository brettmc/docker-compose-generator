FROM php:7.4-cli-alpine as base
WORKDIR /srv/app
ENTRYPOINT ["bin/console.php"]
FROM base as builder
RUN apk add unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
COPY ./composer.* /srv/app/
RUN     composer install --no-dev
COPY . /srv/app/
FROM base
COPY --from=builder /srv/app /srv/app
