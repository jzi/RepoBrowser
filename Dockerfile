FROM php:8.1.0-apache

RUN a2enmod rewrite

RUN apt-get update \
  && apt-get install -y libzip-dev npm git bash libpq-dev libzip-dev unzip wget --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN docker-php-ext-install pdo pdo_pgsql zip

RUN groupadd --gid 1000 repobrowser && \
    useradd --uid 1000 --gid repobrowser --shell /bin/bash --create-home repobrowser

COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /home/repobrowser/project

CMD ["apache2-foreground"]

ENTRYPOINT ["/entrypoint.sh"]
