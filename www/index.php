<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));


// Enable Tracy for error visualization
Tracy\Debugger::enable(APPLICATION_ENV === 'development' ? Tracy\Debugger::DEVELOPMENT : Tracy\Debugger::PRODUCTION);
// Also report E_NOTICE and E_WARNING
Tracy\Debugger::$strictMode = true;

require_once(APPLICATION_PATH . '/init.php');

$application->run();
