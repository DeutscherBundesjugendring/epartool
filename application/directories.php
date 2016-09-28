<?php 

$rootPath = dirname(dirname(__FILE__));

define('MEDIA_URL', '/www/media');
define('MEDIA_PATH', realpath($rootPath . '/' . MEDIA_URL));
define('VENDOR_PATH', realpath($rootPath . '/vendor'));
define('RUNTIME_PATH', realpath($rootPath . '/runtime'));