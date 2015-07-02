DBJR Project Documentation
==========================

## Overview
The main dbjr/tool application (tool) is maintained as a library that is to be used only as a dependency of a dbjr/tool
project. The tool and the project are closely tied together and the project structure has to provide all the necessary
files at the locations expected by the dbjr/tool application. As the file structure expected by the tool might change
between versions, it is necessary to ensure that the project and the tool are of compatible releases. [Semantic
versioning](http://semver.org) is being used.

The tool is written with multi-project support. It means that one database can be used by several projects running on
different URLs and even on different physical hardware. Each project then installs its own instance of the tool as
a dependency, but they can share some consultations among each other.

## The Project
The structure of the project is as follows:

    ├── project                               project specific files
        ├── configs
            ├── application.ini               overloads the dbjr/tool application.ini
            ├── application.local.ini         overloads application.ini
            ├── congig.ini                    overloads the dbjr/tool config.ini
            └── congig.local.ini              overloads config.ini
        ├── css
            └── style.css                     project specific css stylesheet
        ├── images
           ├── logo.png                       project logo
            ├── sponsor-image-1.png           sponsor logo 1 (defined in configs/config.ini)
            ├── sponsor-image-2.png           sponsor logo 2 (defined in configs/config.ini)
            ├── ...
            └── sponsor-image-n.png           sponsor logo n (defined in configs/config.ini)
        └── favicon.ico                       project favicon file
    ├── media                                 user uploaded files
        ├── consultations                     consultations specific uploads
        └── misc                              other uploads
    ├── runtime                               files created at runtime
        ├── cache                             application cache
            └── media                         holds cached media files
        ├── logs                              application logs
            └── mail                          in development mode email are saved here
        └── sessions                          application sessions
    ├── vendor                                composer dependencies (created automatically)
    ├── .htaccess
    ├── build.sh                              build script
    ├── cli.php                               entry point for console access
    ├── composer.json                         defines what dependencies are needed by the project
    ├── Gruntfile.js                          optional; defines build tasks to be run at the project level
    ├── package.json                          optional; defines the npm dependencies to be run at the project level
    ├── index.php                             entry point for http access
    ├── init.php
    └── README.md                             that's the file you are reading now

## Installation
Prior to installing the software, it is necessary to build it. The build of the tool is performed by Composer by
a checkout from a private repository. For this purpose a special limited privilege BitBucket user has been created. The
tool has dependencies of its own, but these are  automatically installed by Composer during the tool installation. There
are also npm and Bower dependencies installed by executing the appropriate commands at the root level of the tool
(`<project_root>/vendor/dbjr/tool`). Finally it is essential to run the required Grunt tasks also at the tool root
level. The only supported build environments are OS X and Linux. Other OS might work as well, but these are not
supported.

### Requirements:

#### Runtime
* PHP 5.4 or later
* PHP modules:
    * GD
* magic_quotes off
* MySQL 5.5 or later
* web server

#### Build Tools
* [Composer](https://getcomposer.org/)
* [Node.js + npm](http://nodejs.org/)
* [Bower](http://bower.io/)
* [Grunt](http://gruntjs.com/)

### Building

#### Automatic Build (Recommended)
It is recommended to build the application using the included build script by running the `build.sh` script with an
argument specifying the branch of tool we wish to use. The available branches are:

* develop
* master
* staging
* production

So for example to checkout the production branch we shall run:

```
build.sh production
```

The programs called by the script can be located at different locations depending on the OS and installation type. It is
expected that users will either modify the build script or write a wrapper script that would set appropriate aliases
to allow `build.sh` to run. The commands that have to be available are:

* `composer`
* `npm`
* `bower`
* `grunt`

#### Manual Build
All the dependencies can also be pulled in and build manually. The order of these commands can not be changed.

1. `$ cd root/of/the/project`
2. Modify `composer.json` to use the desired branch of dbjr/tool
3. `$ composer update`
4. `$ npm update`
5. `$ bower update`
6. `$ cd vendor/dbjr/tool`
7. `$ npm update`
8. `$ bower update`
9. `$ grunt`

Once the build has been successfully performed, the actual deployment can be performed by standard means (FTP, SSH,
etc.).

### Settings
Once the application has been built, several environment-specific settings have to be made. The `*.local.ini` files
might have to be created at this point.

* Database settings in `project/config/application.local.ini`
* SMTP settings in `project/config/application.local.ini` (optional, application defaults to php `mail()` function)
* `APPLICATION_ENV` has to be set in `index.php`. Its value has to match the name of the section to be used in config
  ini files. This section has to be present in **all** ini files.
* Folder permissions:
    + read: `/`
    + read+write: `/runtime/*/`
    + read+write: `/media/*/`

## Upgrading
**PLEASE MAKE SURE THAT YOUR DB AND FILES ARE ALL BACKED UP BEFORE PERFORMING AN UPDATE.**

If a new version of the application is released, the update process is analogous to the installation process. Apart from
updating the project files and all its dependencies, a database patch might have to be applied. The patches will be
located in the `vendor/dbjr/tool/data/db-migrations`. Please see the release notes and upgrade notes for the version
being installed for more details about how to proceed.
