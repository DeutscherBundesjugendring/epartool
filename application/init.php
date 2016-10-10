<?php

require_once __DIR__ . '/directories.php';

require_once(VENDOR_PATH . '/autoload.php');

set_include_path(
    implode(PATH_SEPARATOR, [
        realpath(APPLICATION_PATH . '/../library'),
        APPLICATION_PATH,
        get_include_path(),
    ])
);


// Enable Tracy for error visualization
Tracy\Debugger::enable(
    APPLICATION_ENV === 'development' || APPLICATION_ENV === 'test'
        ? Tracy\Debugger::DEVELOPMENT
        : Tracy\Debugger::PRODUCTION
);
// Also report E_NOTICE and E_WARNING
Tracy\Debugger::$strictMode = true;


// Init configs
$appConfig = new Zend_Config_Ini(
    APPLICATION_PATH . '/configs/application.ini',
    APPLICATION_ENV,
    ['allowModifications' => true]
);

if (is_file(APPLICATION_PATH . '/configs/config.local.ini')) {
    $appConfigLocal = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.local.ini');
    $env = APPLICATION_ENV;
    if (isset($appConfigLocal->$env)) {
        $appConfig->merge($appConfigLocal->$env);
    }
}

$application = new Zend_Application(APPLICATION_ENV, $appConfig);
$application = $application->bootstrap();
