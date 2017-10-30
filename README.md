#ePartool Readme

The tool is written with multi-project support. It means that one database can be used by several projects running as separate instances. Some entities such as consultations and articles can be shared, others are project specific.

## Requirements:

* PHP 7.0
* PHP extensions:
    * GD
    * mbstring
    * pdo_mysql
* MySQL 5.6 database with `utf8mb4_unicode_ci` encoding
* memory_limit: 256M


## Installation

### Wizzard
The preferred way of installation is to use the bundled installation wizard. If the application is not installed, the installation wizard starts automatically. 

After installation is completed, remove the install directory.

### No wizard
The tool uses the Robo task runner to perform the build. It is invoked by running the `$ robo build` command in the root folder where the `RoboFile.php` is located. The following tool are needed:

* [Robo](http://robo.li/)
* [Composer](https://getcomposer.org/)
* [Node.js + npm](http://nodejs.org/)
* [Bower](http://bower.io/)
* [Grunt](http://gruntjs.com/)
* [Webpack](https://webpack.github.io/)

Once the application has been built, several environment-specific settings have to be made.

* Create the environment specific configuration file `application/configs/config.local.ini`.
You can use the file `application/configs/config.local-example.ini` as a template and edit the values.
* Optionally set the `APPLICATION_ENV` in `index.php` to `development`.
* Folder permissions:
    + read: `/`
    + read+write: `/runtime/logs/`
    + read+write: `/runtime/sessions/`
    + read+write: `/runtime/cache/`
    + read+write: `/www/media/consultations/`
    + read+write: `/www/media/folders/`
    + read-write: `/www/image_cache/`
* Remove the install directory.

### Cronjob
Regardless of the installation method, it is needed to configure a cronjob to send a GET request to the page `/cron/execute/key/<secret_cron_key>`. `<secret_cron_key>` is defined in `application/config/config.local.ini`.


## Upgrading

Upgrading the tool version consists of the following steps:
1. Updating the project files
2. Building the application
3. Applying database patches


## Dev mode

The recommended way to develop the tool is using docker. The application in development mode is served at `http://devel.localhost`. First run of docker can take a few minutes.

### Linux
The `docker-compose` tool is needed.

* `$ docker-compose up` to start server
* `$ docker-compose stop` to stop server
 
### Windows and MacOS
It is recommended to use `docker-compose-sync` for better performance.
Installation guide for specific operating system can be found on following websites:

* For Windows: https://github.com/EugenMayer/docker-sync/wiki/docker-sync-on-Windows
* For Mas OS: https://github.com/EugenMayer/docker-sync/wiki/docker-sync-on-Windows

`docker-compose` command is part of Docker and it is not required to install it again.

* `./docker-compose-sync.sh start` to start server
* `./docker-compose-sync.sh stop` to stop server
