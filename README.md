# ePartool

The tool is written with multi-project support. It means that one database can be used by several projects running as separate instances. Some entities such as consultations and articles can be shared, others are project specific.

## <a name="requirements"></a>Requirements

* PHP 7.0
* PHP extensions:
    * GD
    * mbstring
    * pdo_mysql
* MySQL 5.6 database with `utf8mb4_unicode_ci` encoding
* memory_limit: 256M


## Installation

### Wizard
The preferred installation method is using the bundled installation wizard.

1. Obtain the installation package by download or by running the `$ robo build` command in the root folder where the `RoboFile.php` is located. 
2. Visit site in browser. If the application is not installed, the installation wizard starts automatically.
3. After installation is completed, remove the `install` directory.

### No Wizard
#### Filesystem
The tool uses the [Robo](http://robo.li) task runner to perform the build. It is invoked by running the `$ robo build` command in the root folder where the `RoboFile.php` is located. The following tools are needed:

* [Robo](http://robo.li)
* [Composer](https://getcomposer.org)
* [Node.js + npm](http://nodejs.org)
* [Bower](http://bower.io)
* [Grunt](http://gruntjs.com)
* [Webpack](https://webpack.github.io)

Once the application has been built, several environment-specific settings have to be configured.

* Create the environment specific configuration file `application/configs/config.local.ini`.
You can use the file `application/configs/config.local-example.ini` as a template and edit the values.
* Optionally set the `APPLICATION_ENV` in `index.php` to `development`.
* Folder permissions:
    * read: `/`
    * read+write: `/runtime/logs/`
    * read+write: `/runtime/sessions/`
    * read+write: `/runtime/cache/`
    * read+write: `/www/media/consultations/`
    * read+write: `/www/media/folders/`
    * read+write: `/www/image_cache/`
* Remove the `install` directory.

#### Database
* Create database (see [Requirements](#requirements))
* Run the following SQL files and commands in the specified order:
    1. `data/create-installation.sql`
    2. `$ robo phinx:migrate production` 
    3. `data/create-project.sql` (ensure the var `@project_code` is set to whatever is specified in the setting `project` in `application/configs/config.local.ini`)
* Optionally run the following SQL files:
    * `data/create-sample-data.sql`
    * `data/create-admin.sql`

Due to security reasons the `create-admin.sql` script creates an admin with no password. Password can be reset using the forgotten password feature of the application itself or the file can be manually tweaked to insert the correct password hash directly.

### After Install Tasks
Regardless of the installation method, the following tasks must be done:

* Configure a Cron job to send a GET request to the page `/cron/execute/key/<secret_cron_key>`. `<secret_cron_key>` is defined in `application/config/config.local.ini` and is used to prevent unauthorized users from triggering the task. This is not needed for development environments as the Cron jobs can be triggered by visiting the path manually. Setting the Cron job to trigger hourly should be enough.
* In case it is not possible to setup Cron job on your server, the fallback Cron system will be activated by leaving the `cron.key` setting empty. Fallback Cron is disabled in development.
* Copy the file `VERSION-example.txt` to `VERSION.txt` and optionally update the new file. The content is used to inform the users of which version of the tool they are using.


## Upgrading

Upgrading the tool version consists of the following steps:
1. Updating the project files
2. Building the application using `$ robo build`
3. Running `$ robo phinx:migrate production` to apply database patches


## Development

The recommended way to develop the tool is using [Docker](https://www.docker.com). The application in development mode is served at `http://devel.localhost`. First run of Docker can take a few minutes.

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

### GEO functionality
This functionality requires some third party components, which are included.

Interactive maps are provided by javascript library [Leaflet](http://leafletjs.com). Map tiles source url is set in application config.ini in the section osm.data_server_url. This is the endpoint, where the JS library retrieves map tiles from.

Static maps images with marker for previews are rendered with built-in PHP service, which uses the same endpoint for retrieving and caching the map tiles.
 
There is no need to have any account or another kind of authorization to access map tiles on free servers. There are also no strict quotas for usage. For better insurance of availability it is possible to host OSM data on the own server or use some commercial solution.

If the application runs over secured connection (https), the geolocation service provided by a web browser API is used in JS library to locate users. In case the application runs over http, this functionality will be probably unavailable.

Open Street Map (OSM) are licensed by [Open Data Commons Open Database License (ODbL)](https://opendatacommons.org/licenses/odbl) and by [Creative Commons 2.0 (CC-BY-SA)](https://creativecommons.org/licenses/by-sa/2.0)
