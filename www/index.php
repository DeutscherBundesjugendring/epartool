<?php

$rootPath = dirname(dirname(__FILE__));

//define('APPLICATION_ENV', 'development');
define('APPLICATION_ENV', 'production');
define('APPLICATION_PATH', realpath($rootPath . '/application'));
define('MEDIA_PATH', realpath($rootPath . '/www/media'));
define('VENDOR_PATH', realpath($rootPath . '/vendor'));
define('RUNTIME_PATH', realpath($rootPath . '/runtime'));

require_once(APPLICATION_PATH . '/init.php');

$application->run();
