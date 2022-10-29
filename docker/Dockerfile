# Use the private prebuilt laravel image
FROM docker.goebel.app/jonas/laraveldocker:8

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
