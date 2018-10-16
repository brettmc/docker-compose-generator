FROM php:7-cli
WORKDIR /srv/app
RUN apt-get update \
    && apt-get install -y unzip
ENTRYPOINT ["bin/console.php"]
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
COPY ./composer.* /srv/app/
RUN     composer install --no-dev
COPY . /srv/app/
