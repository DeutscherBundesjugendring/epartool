<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

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



/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$appConfig = new Zend_Config_Ini(
    APPLICATION_PATH . '/configs/application.ini',
    APPLICATION_ENV,
     array('allowModifications' => true)
);

$appConfigLocal = new Zend_Config_Ini(
    PROJECT_PATH . '/configs/application.ini',
    APPLICATION_ENV
);

$appConfig->merge($appConfigLocal);

$application = new Zend_Application(
    APPLICATION_ENV,
    $appConfig
);

$application->bootstrap()
            ->run();
