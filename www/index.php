<?php

$rootPath = dirname(dirname(__FILE__));
define('APPLICATION_ENV', 'production');
define('APPLICATION_PATH', realpath($rootPath . '/application'));

$dir = dirname(__FILE__) . '/../';
if (!realpath($dir . '/application/configs/config.local.ini')) {
    if (!empty($_GET['r'])) {
        die('Cannot find installation wizard.');
    }
    $url = sprintf(
        'http%s://%s%s/install/?r=1',
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 's' : '',
        $_SERVER['HTTP_HOST'],
        preg_replace(sprintf('#^%s#', $_SERVER['DOCUMENT_ROOT']), '', realpath($dir))
    );
    header('Location: ' . $url);
    die('You are being redirected to the installation wizard.'
        . sprintf(' If nothing happens please navigate to the <a href="%s">installation wizard</a> manualy', $url));
} elseif (realpath($dir . '/install')) {
    die('Please remove the /install folder. Not removing it is a security risk.');
}

require_once(APPLICATION_PATH . '/init.php');

$application->run();
