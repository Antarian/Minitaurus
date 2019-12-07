# development docker file
FROM php:7.2-cli

LABEL minitaurus-dockerfile=Minitaurus

# prepare php ini settings
RUN mkdir /usr/src/php
RUN tar --file /usr/src/php.tar.xz --extract --strip-components=1 --directory /usr/src/php
RUN cp /usr/src/php/php.ini-development /usr/local/etc/php/php.ini

# prepare workdir
RUN mkdir -p /usr/src/minitaurus;
WORKDIR /usr/src/minitaurus
