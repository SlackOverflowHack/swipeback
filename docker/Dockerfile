# Use the official image as a parent image
FROM ubuntu

# Set the working directory
WORKDIR /var/www/html

# disable frontend terminal questions while installing apt packages (problem with php tzdata)
ARG DEBIAN_FRONTEND=noninteractive

# set the correct timezone
ENV TZ=Europe/Berlin
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Run the command inside your image filesystem
RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y software-properties-common git
RUN add-apt-repository ppa:ondrej/php -y
RUN apt-get install -y php7.4 php7.4-common php7.4-dom php7.4-mbstring php7.4-xsl zip unzip php7.4-zip php7.4-mysql curl php7.4-curl php7.4-gd php7.4-pcov sqlite3 php7.4-sqlite3
RUN apt-get update

# install composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm composer-setup.php

# apache2 needs to be able to handle / links
RUN a2enmod rewrite

# enable proxy for surveillance camera
RUN a2enmod proxy && a2enmod proxy_http && a2enmod proxy_html && a2enmod headers

# remove the default apache index.html
RUN rm index.html

# Automatically install laravel on top of the needed files
RUN composer create-project laravel/laravel:8 .
RUN composer install

# create folders if they dont exist
RUN mkdir -p bootstrap/cache
RUN mkdir -p storage/logs/
RUN mkdir -p storage/framework/sessions
RUN mkdir -p storage/framework/views
RUN mkdir -p storage/framework/cache

# remove example tests left from laravel/laravel
RUN rm /var/www/html/tests/Unit/ExampleTest.php
RUN rm /var/www/html/tests/Feature/ExampleTest.php


# add healthcheck
HEALTHCHECK CMD curl --fail http://localhost:80 || exit 1

# Set the working directory
WORKDIR /var/www/html

RUN mkdir -p bootstrap/cache

# copy env if no env defined
RUN if test -f "/var/www/html/.env"; then echo "using existing .env file"; else cp "/var/www/html/.env-docker" "/var/www/html/.env"; fi

# install php-ldap extension
RUN apt update
RUN apt install -y php7.4-ldap php7.4-mongodb

# Copy the rest of your app's source code from your host to your image filesystem
COPY . .

RUN composer update

RUN cp /var/www/html/docker/apacheConf.conf /etc/apache2/sites-enabled/000-default.conf

# Automatically install laravel on top of the needed files
RUN composer install

# set the laravel encryption key
RUN php artisan key:generate

# use the .env-docker file if we have a env for docker
RUN if test -f "/var/www/html/.env-docker"; then mv /var/www/html/.env-docker /var/www/html/.env; fi

# create logfile to make sure it won't be created by root
RUN echo "" > /var/www/html/storage/logs/laravel.log

# set folder permissions
RUN chown -R www-data:www-data /var/www/html

# Inform Docker that the container is listening on the specified port at runtime.
EXPOSE 80

# Run the specified command within the container.
CMD /bin/bash docker/start.sh
