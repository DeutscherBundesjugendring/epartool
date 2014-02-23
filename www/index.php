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

// initialize Composer autoloading
require '../vendor/autoload.php';

/** Zend_Application */
require_once 'Zend/Application.php';


// merge main config with envspecific
$config = new Zend_Config_Ini(
    APPLICATION_PATH . '/configs/application.ini',
    APPLICATION_ENV,
    array('allowModifications' => true)
);
if (is_file(APPLICATION_PATH . '/configs/application-envspecific.ini')) {
    $configLocal = new Zend_Config_Ini(
        APPLICATION_PATH . '/configs/application-envspecific.ini',
        APPLICATION_ENV
    );
    $config->merge($configLocal);
}


$application = new Zend_Application(
    APPLICATION_ENV,
    $config
);

$application = $application->bootstrap();

// check if http authentication is required and crdentials
if (Zend_Registry::get('systemconfig')->httpAuth->active) {
    if (empty($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="Protected"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Login canceled by user.';
        exit;
    }

    if (
        $_SERVER['PHP_AUTH_USER'] != Zend_Registry::get('systemconfig')->httpAuth->username
        || $_SERVER['PHP_AUTH_PW'] != Zend_Registry::get('systemconfig')->httpAuth->password
    ) {
        header('HTTP/1.0 401 Unauthorized');
        echo 'Wrong credentials!';
        exit;
    }
}

$application->run();
