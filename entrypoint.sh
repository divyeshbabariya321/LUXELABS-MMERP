#!/bin/bash
param=$1

echo "Starting $param";
cd /var/www
if [ "$param" == "ERP_APP" ]
then
   php-fpm -R & php artisan websockets:serve & nginx -g "daemon off;" 
elif [ "$param" == "ERP_APP_HORIZON" ]
then
   php-fpm -R & php artisan horizon & php artisan websockets:serve 
elif [ "$param" == "ERP_HORIZON" ]
then
   service supervisor start
   php-fpm -R & php artisan horizon & nginx -g "daemon off;" 
elif [ "$param" == "ERP_TOTEM" ]
then
   php artisan totem:assets   
   php-fpm -R & nginx -g "daemon off;" & php artisan schedule:work 
elif [ "$param" == "ERP_MIGRATION" ]
then
   php artisan migrate --force
else
   echo "Command not found and running php"
   php $param
   #exit 1
fi
