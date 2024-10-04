## Project specification

- PHP Deployer 7

## System requirement

Ensure the followings are installed on your local machine:

- PHP 8.2 or newer
- Composer 2 package manager
- Node.js 20 is preferable but other versions should still work well
- Yarn package manager
- AWS CLI for SPA deployment on S3

## Preparation

- Install dependencies by running `composer install`
- Dump composer autoload by running `composer dump-autoload`
- Prepare `.env` file following `.env.example`
- Register SSH public key to be able to clone git using SSH

## Deployment

### API

#### Development:

```shell
dep -f api/development/deploy.php deploy
```

or

```shell
sh commands/deploy-api-dev.sh
```

#### Staging:

```shell
dep -f api/staging/deploy.php deploy
```

or

```shell
sh commands/deploy-api-stg.sh
```

#### Production:

```shell
dep -f api/production/deploy.php deploy
```

or

```shell
sh commands/deploy-api-prod.sh
```

### SPA

Staging:

```shell
dep -f spa/staging/deploy.php deploy
```

Production:

```shell
dep -f spa/production/deploy.php deploy
```

## EC2 instance requirement

- OS: Linux
- PHP 8.2
- PHP-FPM
- Composer 2
- Git
- Nginx
- Crond to manage Laravel Scheduler (Scheduled jobs)

## EC2 instance role

### Staging

- xxx.xxx.xxx.xxx: application server and worker server
- xxx.xxx.xxx.xxx: application server

### Production

- xxx.xxx.xxx.xxx: application server and worker server
- xxx.xxx.xxx.xxx: application server

## EC2 instance startup procedure

### For application server *(all EC2 instances)*:

1. Restart Nginx: `sudo systemctl restart nginx`
2. Restart PHP-FPM: `sudo systemctl restart php-fpm`

### For worker server *(staging: xxx.xxx.xxx.xxx, production: xxx.xxx.xxx.xxx)*:

1. Restart Supervisord and Horizon:
    - `sudo systemctl restart supervisord`
    - `sudo systemctl restart horizon`
2. Restart Crond: `sudo systemctl restart crond`