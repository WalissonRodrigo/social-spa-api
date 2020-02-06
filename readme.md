# social
> A Laravel project

## Before Install
> configure [.env](.env) file with database credentials and database name (created previous)

## Install Setup

``` bash
# install dependencies
composer install

# configure passport
php artisan passport:install

# run migrations in database
php artisan migrate --step

# run server in port 8000 in localhost for correct call in frontend
php artisan serve --host=localhost --port=8000
