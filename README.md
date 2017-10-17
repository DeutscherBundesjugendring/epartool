#ePartool Readme

The tool is written with multi-project support. It means that one database can be used by several projects running
as separate instances. Some entities can be shared, others are project specific.

## Installation

The preferred way of installation is to use the bundled installation wizard. If the application is not installed,
the installation wizard starts automatically.

### Requirements:

#### Runtime
* PHP 7.0
* PHP extensions:
    * GD
    * mbstring
    * pdo_mysql
* MySQL 5.6 database with utf8 encoding
* memory_limit: 256M

#### Build Tools
* [Robo](http://robo.li/)
* [Composer](https://getcomposer.org/)
* [Node.js + npm](http://nodejs.org/)
* [Bower](http://bower.io/)
* [Grunt](http://gruntjs.com/)
* [Webpack](https://webpack.github.io/)

### Building

The tool uses the Robo task runner to perform the build. It is invoked by running the `$ robo build` command.

### Settings
Once the application has been built, several environment-specific settings have to be made.

* Create the environment specific configuration file `application/configs/config.local.ini`.
You can use the file `application/configs/config.local-example.ini` as a template and edit the values.
* If needed set the `APPLICATION_ENV` in `index.php` to `development`.
* Folder permissions:
    + read: `/`
    + read+write: `/runtime/*/`
    + read+write: `/media/*/`
* Configure a cronjob to to send a GET request to the page `/cron/execute/key/<secret_cron_key>` or by

## Upgrading

Upgrading the tool version consists of the following steps:
1. Updating the project files
2. Run `robo update`
3. Apply database patch if needed. All patches are located in `data/db-migrations` and in `data/phinx-migrations`.


# How to use ePartool in dev environment

On Linux based operating system you need to use `docker-compose` command. On Windows and MacOS its recommended
to use `docker-compose-sync` for better performance.

## Installation

Beside standard `docker-compose`,  `docker-sync` and `unison` are required for running this application on Windows and
MacOS with better performance. Installation guide for specific operating system can be found on following websites:

* For Windows: https://github.com/EugenMayer/docker-sync/wiki/docker-sync-on-Windows
* For Mas OS: https://github.com/EugenMayer/docker-sync/wiki/docker-sync-on-Windows

`docker-compose` command is part of Docker and it is not required to install it again.

## Use

On Linux based operating system:

* `docker-compose up` or `docker-compose start` to start server
* `docker-compose stop` to stop server

On Windows and Mac OS using `docker-compose-sync`:

* `./docker-compose-sync.sh start` to start server
* `./docker-compose-sync.sh stop` to stop server

## Caution

First run of both start commands can take a few minutes. First run of `docker-compose-sync` will last longer
because of initial synchronization of all files. You can set `verbose: true` in `docker-sync.yml` if you want be sure
that files are being synchronized correctly.
