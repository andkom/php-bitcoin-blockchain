FROM php:7.2-cli

RUN apt-get update \
    && apt-get install -y libleveldb-dev libgmp-dev \
    && pecl install leveldb-0.2.1 \
    && docker-php-ext-enable leveldb \
    && docker-php-ext-install gmp