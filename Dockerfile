FROM php:7.3-cli-alpine
WORKDIR /srv/app
RUN apk add unzip
ENTRYPOINT ["bin/console.php"]
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
COPY ./composer.* /srv/app/
RUN     composer install --no-dev
COPY . /srv/app/
