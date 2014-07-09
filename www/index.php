<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(APPLICATION_PATH . '/../library'),
            realpath(APPLICATION_PATH . '/../vendor'),
            get_include_path(),
        )
    )
);

// Enable Tracy for error visualization
Tracy\Debugger::enable();
// Also report E_NOTICE and E_WARNING
Tracy\Debugger::$strictMode = true;

// Zend_Application
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$appConfig = new Zend_Config_Ini(
    APPLICATION_PATH . '/configs/application.ini',
    APPLICATION_ENV,
    array('allowModifications' => true)
);

$appConfigProject = new Zend_Config_Ini(
    PROJECT_PATH . '/configs/application.ini',
    APPLICATION_ENV
);
$appConfig->merge($appConfigProject);

if (is_file(PROJECT_PATH . '/configs/application.local.ini')) {
    $appConfigLocal = new Zend_Config_Ini(
        PROJECT_PATH . '/configs/application.local.ini'
    );
    $env = APPLICATION_ENV;
    if (isset($appConfigLocal->$env)) {
        $appConfig->merge($appConfigLocal->$env);
    }
}

$application = new Zend_Application(
    APPLICATION_ENV,
    $appConfig
);

$application = $application->bootstrap();

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
