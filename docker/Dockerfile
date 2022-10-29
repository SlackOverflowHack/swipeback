# Use the private prebuilt laravel image
FROM docker.goebel.app/jonas/laraveldocker:8

# add healthcheck
HEALTHCHECK CMD curl --fail http://localhost:80 || exit 1

# install nodejs & npm for vue blade compiling
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - && apt-get install -yq nodejs build-essential

# Set the working directory
WORKDIR /var/www/html

RUN mkdir -p bootstrap/cache

# create folders if they dont exist
RUN mkdir -p database/seeds

# copy env if no env defined
RUN if test -f "/var/www/html/.env"; then echo "using existing .env file"; else cp "/var/www/html/.env-docker" "/var/www/html/.env"; fi

# install php-ldap extension
RUN apt update
RUN apt install -y php7.4-ldap php7.4-mongodb

# Copy the rest of your app's source code from your host to your image filesystem
COPY . .

RUN composer update

# patch files from directorytree generating problems with openldap
RUN mkdir -p /var/www/html/vendor/directorytree/ldaprecord/src/Models/ActiveDirectory/Scopes
RUN cp /var/www/html/vendorpatch/directorytree/ldaprecord/src/Models/ActiveDirectory/User.php /var/www/html/vendor/directorytree/ldaprecord/src/Models/ActiveDirectory/User.php
RUN cp /var/www/html/vendorpatch/directorytree/ldaprecord/src/Models/ActiveDirectory/Scopes/RejectComputerObjectClass.php /var/www/html/vendor/directorytree/ldaprecord/src/Models/ActiveDirectory/Scopes/RejectComputerObjectClass.php

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
#CMD ["/bin/bash" , "docker/start.sh"]
CMD /bin/bash docker/start.sh
#CMD /bin/bash docker/wait-for-it.sh mysql:3306 -- /bin/bash", docker/start.sh
