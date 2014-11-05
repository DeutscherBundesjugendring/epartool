<?php

if (php_sapi_name() !== 'cli') {
    die('This script may only be run form CLI.');
}

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__)));

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
