#!/bin/sh

set -e

echo "Clearing cache..."
php artisan config:clear
php artisan cache:clear

echo "Running migrations..."
php artisan migrate --force


echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=${PORT}
