<?php

$rootPath = dirname(dirname(__FILE__));
define('APPLICATION_ENV', 'test');
define('APPLICATION_PATH', realpath($rootPath . '/application'));

require_once(APPLICATION_PATH . '/init.php');

$application->run();
