<?php

// Script taken from: https://github.com/robmorgan/phinx/issues/137#issuecomment-26220408


// Comment out to run migrations.
die('This file must be edited on the filesystem before you can proceed. Please open the file www/runMigrations.php in your favorite editor and comment out the die() statement at the beginning of the file.');

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);

require_once '../vendor/autoload.php';
require_once '../application/services/BufferedOutput.php';
require_once '../application/services/PhinxMigrate.php';

$phinxMigrate = new Service_PhinxMigrate('production');
$phinxMigrate->run();
echo $phinxMigrate->getOutput();
