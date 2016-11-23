# Blockland Repository

Source for Blockland Repository to store Add-Ons for [Blockland](http://blockland.us/).

## DISCLAIMER

**This project is still in development and should _not_ be used in production!**

Due to that this project is not even in Beta, this will result in drastically changes that will break your installation if you choose to install this on your server.

## Description

This project is created as an alternative for the previous closed Return To Blockland Add-On hosting site for the game Blockland. Additionally it is released with full source for anyone to download, modify and/or re-release however they like.

The intention is to create a freedom for the Blockland community of where to put their Add-Ons but also a innovative interface to make it easier to distribute Add-Ons to their users. It should also ease the communication between hosts by transferring Add-Ons between servers to ensure that Add-Ons survive if a server goes down permanently.

## Installation

The project is using the framework [Laravel](https://laravel.com/) (v5.3) which should make it quite easy to install on any server with php 5.6 or above. Custom modifications is also easy due to this fact.

First download the source to your server.

```bash
wget https://github.com/McTwist/Blockland-Repository/archive/master.zip
```

Update Laravel.

```bash
composer update
composer dump-autoload
```

Copy .env.example to .env and then generate a key.

```bash
php artisan key:generate
```

Make the necessary changes in .env. For production, this is an example of howthe file could look like.

```env
APP_ENV=production
APP_KEY=base64:TAutB1p/tlH4qXs2xvQ+Yi64Kya27/Ibh50tRsC2miI=
APP_DEBUG=false
APP_LOG_LEVEL=debug
APP_URL=http://example.com
APP_TIMEZONE=Europe/Stockholm

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=repository
DB_USERNAME=user
DB_PASSWORD=password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync
```

Then prepare compiled files and database with the following commands.

```bash
php artisan optimize
php artisan migrate
php artisan db:seed
```

The last command seeds the database with recommended categories and an admin account.

```
Username: admin
Password: password
```

### Update

Whenever you pull a new version from the repo, you ought to do the following to make sure that everything is merged properly.

```
composer update
composer dump-autoload
php artisan optimize
php artisan migrate
```

There probably wont be any more seeding, so all new updates on that will either be automatically handled, or you need to make your own changes to make this work properly.


## Current contributors

* [McTwist](https://github.com/McTwist) (Development)
* [Boom](https://github.com/Boomshicleafaunda) (Laravel Master)
* [Demian](https://github.com/DemianWright) (Graphics, Design)


