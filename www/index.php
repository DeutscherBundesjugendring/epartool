<?php

$rootPath = dirname(dirname(__FILE__));

define('APPLICATION_ENV', 'development');
//define('APPLICATION_ENV', 'production');
define('APPLICATION_PATH', realpath($rootPath . '/application'));
define('MEDIA_PATH', realpath($rootPath . '/www/media'));
define('VENDOR_PATH', realpath($rootPath . '/vendor'));
define('RUNTIME_PATH', realpath($rootPath . '/runtime'));

set_include_path(APPLICATION_PATH);
require_once(VENDOR_PATH . '/autoload.php');
require_once(APPLICATION_PATH . '/init.php');

// Enable Tracy for error visualization
Tracy\Debugger::enable(APPLICATION_ENV === 'development' ? Tracy\Debugger::DEVELOPMENT : Tracy\Debugger::PRODUCTION);
// Also report E_NOTICE and E_WARNING
Tracy\Debugger::$strictMode = true;

$application->run();
