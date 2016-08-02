<?php

if (php_sapi_name() !== 'cli') {
    die('This script may only be run form CLI.');
}

$rootPath = dirname(dirname(__FILE__));
define('APPLICATION_ENV', 'production');
define('APPLICATION_PATH', realpath($rootPath . '/application'));

require_once(APPLICATION_PATH . '/init.php');

$help = <<<EOD
A cli interface to manage the dbjr tool application from command line.

Usage: cli.php <command>

Commands:
cron      - Runs the cronjob task by utlising the cron service

EOD;


if ($argc === 1) {
    echo $help;
} elseif ($argc === 2 && $argv[1] === 'cron') {
    Service_Cron::executeAll();
    echo "Cron task completed succesffuly\n";
}
