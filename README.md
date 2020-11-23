Application is written as Single Page Application with Vue components and Laravel-Sanctum for authentication and authorization

## How to install
### Requirements
Latest versions of docker and docker-compose need to be installed

### Steps
1. Clone this repository
2. Go to the created directory
3. Run <br/>```docker-compose up -d```
4. Install all dependencies <br/>
```docker-compose exec php-fpm composer install --no-interaction --optimize-autoloader```
5. Copy the .env file <br/>
```docker-compose exec php-fpm cp .env.example .env```
6. Generate a unique application key <br/>
```docker-compose exec php-fpm php artisan key:generate```
7. Run database migrations <br/>
```docker-compose exec php-fpm php artisan migrate --no-interaction```
8. Add your api key as KLAVIYO_TOKEN to the .env file
9. Open your browser and go to the http://localhost:8000

If there is a problem with permissions, run on your host machine (with sudo if needed): <br/>
```chown -R www-data:www-data storage``` 

### Import data
To the repository there is contacts.csv file attached that can be used to import some contacts

## To run api tests
Run <br/>
```docker-compose exec php-fpm php artisan test```