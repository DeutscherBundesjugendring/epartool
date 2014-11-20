<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));


// Enable Tracy for error visualization
Tracy\Debugger::enable(APPLICATION_ENV === 'development' ? Tracy\Debugger::DEVELOPMENT : Tracy\Debugger::PRODUCTION);
// Also report E_NOTICE and E_WARNING
Tracy\Debugger::$strictMode = true;


require_once(APPLICATION_PATH . '/init.php');


// Check if http authentication is required and crdentials
if (!empty(Zend_Registry::get('systemconfig')->httpAuth->active)) {
    if (empty($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="Protected"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Login canceled by user.';
        exit;
    }

    if ($_SERVER['PHP_AUTH_USER'] != Zend_Registry::get('systemconfig')->httpAuth->username
        || $_SERVER['PHP_AUTH_PW'] != Zend_Registry::get('systemconfig')->httpAuth->password
    ) {
        header('HTTP/1.0 401 Unauthorized');
        echo 'Wrong credentials!';
        exit;
    }
}

$application->run();
