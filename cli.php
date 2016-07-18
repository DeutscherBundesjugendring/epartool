<?php

if (php_sapi_name() !== 'cli') {
    die('This script may only be run form CLI.');
}

// define('APPLICATION_ENV', 'production');
define('APPLICATION_ENV', 'development');

require('init.php');
require('vendor/dbjr/tool/application/cli.php');
