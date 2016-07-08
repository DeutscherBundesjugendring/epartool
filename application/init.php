<?php

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path(),
        )
    )
);

require_once 'Zend/Application.php';

$appConfig = new Zend_Config_Ini(
    APPLICATION_PATH . '/configs/application.ini',
    APPLICATION_ENV,
    array('allowModifications' => true)
);

if (is_file(APPLICATION_PATH . '/configs/config.local.ini')) {
    $appConfigLocal = new Zend_Config_Ini(
        APPLICATION_PATH . '/configs/config.local.ini'
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
