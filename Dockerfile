FROM php:8.0.28-fpm


RUN apt update \
        && apt install -y bzip2 libbz2-dev g++ libcurl4 libcurl4-openssl-dev libgmp-dev zip \
            libicu-dev libonig-dev libpq-dev libxml2-dev libzip-dev postgresql-client vim-tiny zlib1g-dev libpng-dev \
        && docker-php-ext-install bz2 curl gmp intl iconv bcmath calendar \
            mbstring opcache pdo pdo_pgsql pgsql xml zip exif gd
RUN apt update && apt-get install nodejs npm mariadb-client -y && apt-get install -y cron awscli

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN cd /usr/local/etc/php/conf.d/ && \
  echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini

RUN docker-php-ext-install mysqli pdo pdo_mysql soap
RUN docker-php-ext-configure pcntl --enable-pcntl && docker-php-ext-install pcntl
