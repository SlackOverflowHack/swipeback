#!/bin/bash

# echo "waiting until mysql is running";
# until echo '\q' | mysql -h"$DB_HOST" -P"$DB_HOST" -u "$DB_USER" --password="$DB_PASSWORD" $DB_DATABASE; do
#     >&2 echo "MySQL is unavailable - sleeping"
#     sleep 1
# done
# echo "mysql is now running";


# set folder permissions
#chown -R www-data:www-data /var/www/html

# set permissions again at runtime to prevent logging errors
#chown www-data:www-data storage/logs -R

# install the database tables
# echo updating database tables;
# php artisan migrate;

# syncing static database tables with config
# echo synchronising static database tables;
# php artisan table:sync;

# launch the application
echo Starting Webserver;
apachectl -D FOREGROUND;
