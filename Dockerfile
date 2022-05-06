FROM php:8.1.0-apache

RUN a2enmod rewrite

RUN apt-get update \
  && apt-get install -y libzip-dev npm git bash libpq-dev libzip-dev unzip wget --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install pdo pdo_pgsql zip;


COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY . /var/www
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www

CMD ["apache2-foreground"]

ENTRYPOINT ["/entrypoint.sh"]
