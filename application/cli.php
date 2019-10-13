<?php

if (php_sapi_name() !== 'cli') {
    // @codingStandardsIgnoreLine
    die('ERROR: This script may only be run from CLI.');
}

if (!extension_loaded('pdo_mysql')) {
    // @codingStandardsIgnoreLine
    die('ERROR: The extension pdo_mysql must be loaded in order to manage the application via CLI.');
}


$rootPath = dirname(dirname(__FILE__));
define('APPLICATION_ENV', 'production');
define('APPLICATION_PATH', realpath($rootPath . '/application'));

require_once(APPLICATION_PATH . '/init.php');

$help = <<<EOD
A cli interface to manage the ePartool application from command line.

Usage: cli.php <command>

Commands:
cron      - Runs the cronjob task by utilising the cron service

EOD;


if ($argc === 1) {
    // @codingStandardsIgnoreLine
    echo $help;
} elseif ($argc === 2 && $argv[1] === 'cron') {
    Service_Cron::executeAll();
    // @codingStandardsIgnoreLine
    echo "Cron task completed successfully\n";
}
