#ePartool Readme

The tool is written with multi-project support. It means that one database can be used by several projects running
as separate instances. Some entities can be shared, others are project specific.

## Installation

The preferred way of installation is to use the bundled installation wizard. If the application is not installed,
the installation wizard starts automatically.

### Requirements:

#### Runtime
* PHP 5.6
* PHP extensions:
    * GD
    * mbstring
    * pdo_mysql
* MySQL 5.5 database with utf8 encoding
* memory_limit: 256M

#### Build Tools
* [Robo](http://robo.li/)
* [Composer](https://getcomposer.org/)
* [Node.js + npm](http://nodejs.org/)
* [Bower](http://bower.io/)
* [Grunt](http://gruntjs.com/)

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
2. Run `robo build`
3. Apply database patch if needed. All patches are located in `data/db-migrations`.
