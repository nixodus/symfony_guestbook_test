## Install Doc

Requiments:

php 7.1
mysql 5.7

After git clone command in console

###composer.phar install
####

#####Set database access in file .env 

Update database struct
#####php ./bin/console doctrine:schema:update --force

#
Command start internal server

####php bin/console server:start
####
load fixtures

####php bin/console doctrine:fixtures:load
####

create admin user

####php bin/console fos:user:create adminuser --super-admin
####

run phpunit
### Warining: phpunit erase current database
####php bin/phpunit


Admin link:
####http://localhost:8000/admin/