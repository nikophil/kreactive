FROM merces/php

COPY ./php.ini /usr/local/etc/php/

RUN apt-get install -y supervisor libxrender1 libfontconfig1
RUN docker-php-ext-install bcmath pcntl
