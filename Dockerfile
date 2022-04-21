FROM debian:stable
SHELL ["/bin/bash", "-c"]

RUN apt-get update
RUN apt-get install -y curl git haxe php7.4-fpm wget php-xml zip p7zip
RUN mkdir ~/haxelib && haxelib setup ~/haxelib
RUN curl -LJO https://github.com/interealm-games/opentask/releases/download/0.4.0/opentask_debian
RUN mv opentask_debian /usr/bin/opentask
RUN chmod +x /usr/bin/opentask
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet
RUN mv composer.phar /usr/local/bin/composer
RUN mkdir -p /server-php
WORKDIR /server-php
COPY . /server-php
WORKDIR /server-php
RUN ls
# RUN opentask rungroup init
RUN opentask rungroup build
