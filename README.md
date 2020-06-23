# Symfony 5 REST API + Docker

Symfony skeleton for REST API application run in docker environment. Make sure you install docker on your machine beforehand (the setup process is not covered here).

> **NOTICE:** `docker-compose.yml` is set up for MacOS so it might need adjustments when running on different OS.

## Project environment
- Symfony 5
- PHP 7.4
- MySQL

## Project setup

Clone project:

```
git clone git@github.com:bkrnetic/undabot_assignment.git
```

Copy `.env.dist` file to `.env` file in root directory. If you wish to use different settings for database, make sure you update environment variables.

File `docker-compose.yml` is set up for running on macOS. If using other OS, make sure you update `docker-compose` file according to your requirements.

Start your docker containers:

```
docker-compose up
```

Enter `web` container:

```
docker-compose exec web sh
```

Install project dependencies:

```
composer install
```

> **NOTICE:** Composer is installed automatically during the first build of docker `web` container (`php-fpm` image) but it might be running slow if using it from inside the container. If you wish to use it outside the container, make sure to have it installed on your local machine. 



Compile `.env` files (`.env.local.php`) to avoid *unknown file* warning in `config/bootstrap` by running:

```
composer dump-env prod
```

Run migrations from inside the `web` container:

```
bin/console doctrine:migrations:migrate
```

> **NOTICE:** Always use `bin/console doctrine:migrations:diff` command to create new migrations. However, if creating migrations manually, run `bin/console doctrine:schema:validate` command afterwards to make sure that mapping and database schema are in sync.
> 
>For more on DoctrineMigrationsBundle, check the following [link](https://symfony.com/doc/master/bundles/DoctrineMigrationsBundle/index.html).

## Testing

Navigate to your localhost followed by `/api/docs` (e.g. `localhost/api/docs`) and test the application using Swagger or try `/api/v1/foo?title=sample`.
