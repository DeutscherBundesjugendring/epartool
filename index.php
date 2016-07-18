<?php

if (!realpath(dirname(__FILE__) . '/application/configs/config.local.ini')) {
    $url = sprintf(
        'http%s://%s',
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 's' : '',
        $_SERVER['HTTP_HOST']
    );
    if ($_SERVER['REQUEST_URI']) {
        $url .= $_SERVER['REQUEST_URI'];
    }
    $url .= 'install';
    header('Location: ' . $url);
    die('You are being redirected to the installation wizard.'
        . ' If nothing happens please navigate to the <a href="/install">installation wizard</a> manualy');
} elseif (realpath(dirname(__FILE__) . '/install')) {
    die('Please remove the /install folder. Not removing it is a security risk.');
}

define('APPLICATION_ENV', 'production');
//define('APPLICATION_ENV', 'development');

require('init.php');
require('www/index.php');
