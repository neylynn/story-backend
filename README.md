# install dependencies
composer install

# create .env file and generate the application key
cp .env.example .env
php artisan key:generate
php artisan config:cache
php artisan migrate

# lunch the server
php artisan serve

# api step
first you need to call register and login API
After that, you can start the story api process by using a token.
I will share the API collection that is included in the email.

# api integration test
create new mysql database for testing
it config is in phpunit.xml file

# test command
php artisan test