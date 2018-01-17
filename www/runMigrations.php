<?php

// Script taken from: https://github.com/robmorgan/phinx/issues/137#issuecomment-26220408


// Comment out to run migrations.
die('Locked over ftp. Open me or go away.');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
require_once '../application/services/BufferedOutput.php';
require_once '../application/services/PhinxMigrate.php';

$phinxMigrate = new Service_PhinxMigrate('production');
$phinxMigrate->run();
echo $phinxMigrate->getOutput();
