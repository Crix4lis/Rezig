> author: Michał Powała <br>
> source repository: [Rezig](https://github.com/Crix4lis/rezig)

# HOW TO RUN
## How to run the stack
1. Clone the repository
1. Inside project directory run docker containers: `docker-compose up -d`
1. Enter php-cli docker container: `docker-compose exec cli bash`
    - If its your first time you run the application install dependencies, inside php-cli container run: `composer install`
1. Inside php-cli docker container run symfony server: `php bin/console server:start *:8000`

## How to run tests
> Remember to follow all previous steps
1. Enter php-cli docker container: `docker-compose exec cli bash`
1. Inside container run: `vendor/bin/phpunit`

## How to run application itself:
> Remember to follow How to run the stack steps
1. Endpoint address is: `GET http://localhost:8000/api/game/1`
1. Available query parametres:
  - `sortByDate`, values: `asc` or `dsc`
  - `sortByScore`, values: `asc` or `dsc`
  - examle: `GET http://localhost:8000/api/game/1?sortByScore=asc`
