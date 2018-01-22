<?php

require('../vendor/autoload.php');
require('src/autoloader.php');
require('../application/services/BufferedOutput.php');
require('../application/services/PhinxMigrate.php');

use Form\InstallForm;
use Util\Config;
use Util\Db;
use Util\FileSystem;
use Util\View;

error_reporting(0);

$scriptPath = realpath(dirname(__FILE__) . '/views/scripts');
$helperPath = realpath(dirname(__FILE__) . '/../application/views/helpers');
$sqlPath = realpath(dirname(__FILE__) . '/../data');
$layoutPath = realpath(dirname(__FILE__) . '/views/layouts');
$langPath = realpath(dirname(__FILE__) . '/languages');
$zendLangPath = realpath(dirname(__FILE__) . '/../vendor/zendframework/zendframework1/resources/languages');

$configPath = realpath(dirname(__FILE__) . '/../application') . '/configs';
$logPath = realpath(dirname(__FILE__) . '/../runtime') . '/logs';
$sessionPath = realpath(dirname(__FILE__) . '/../runtime') . '/sessions';
$cachePath = realpath(dirname(__FILE__) . '/../runtime') . '/cache';
$imgCachePath = realpath(dirname(__FILE__) . '/../www') . '/image_cache';
$consultationsPath = realpath(dirname(__FILE__) . '/../www/media') . '/consultations';
$foldersPath = realpath(dirname(__FILE__) . '/../www/media') . '/folders';

$locale = !empty($_GET['locale']) ? $_GET['locale'] : null;


$view = new View($layoutPath, $scriptPath, $helperPath, $zendLangPath, $langPath, $locale);
$fileSystem = new FileSystem(
    $configPath,
    $logPath,
    $sessionPath,
    $cachePath,
    $imgCachePath,
    $consultationsPath,
    $foldersPath
);

if (!isset($locale)) {
    $view->render('step-1.phtml');
} elseif (file_exists($configPath . '/config.local.ini')) {
    $view->render('already-installed.phtml');
} elseif (!$fileSystem->validateWritable()) {
    if (!$fileSystem->createFolders()) {
        $view->assign([
            'configPathOK' => is_writable($configPath),
            'sessionPathOK' => is_writable($sessionPath),
            'logPathOK' => is_writable($logPath),
            'cachePathOK' => is_writable($cachePath),
            'imgCachePathOK' => is_writable($imgCachePath),
            'consultationsPathOK' => is_writable($consultationsPath),
            'foldersPathOK' => is_writable($foldersPath),
            'configPath' => $configPath,
            'sessionPath' => $sessionPath,
            'logPath' => $logPath,
            'cachePath' => $cachePath,
            'imgCachePath' => $imgCachePath,
            'consultationsPath' => $consultationsPath,
            'foldersPath' => $foldersPath,
        ]);
        $view->render('file-permissions.phtml');
    }
}


$form = new InstallForm();
if (!empty($_POST)) {
    if ($form->isValid($_POST)) {
        try {
            $db = new Db($_POST['dbName'], $_POST['dbHost'], $_POST['dbUsername'], $_POST['dbPass']);
            $db->initDb($sqlPath, $_POST['adminName'], $_POST['adminEmail'], $_POST['adminPassword'], $_POST['locale']);

            $config = new Config(new Zend_Config_Writer_Ini(), $configPath);
            $config->writeConfigLocalIni(
                [
                    'host' => $_POST['dbHost'],
                    'name' => $_POST['dbName'],
                    'userName' => $_POST['dbUsername'],
                    'password' => $_POST['dbPass']
                ],
                [
                    'fromAddress' => $_POST['emailFromAddress'],
                    'fromName' => $_POST['emailFromName'],
                    'replyToAddress' => $_POST['emailReplyToAddress'],
                    'replyToName' => $_POST['emailReplyToName'],
                    'smtp' => [
                        'auth' => $_POST['emailSmtpAuth'],
                        'port' => $_POST['emailSmtpPort'],
                        'ssl' => $_POST['emailSmtpSsl'],
                        'host' => $_POST['emailSmtpHost'],
                        'password' => $_POST['emailSmtpPass'],
                        'username' => $_POST['emailSmtpUserName'],
                    ]
                ],
                'xx',
                $_POST['cronKey'],
                $_POST['googleId'],
                $_POST['googleSecret'],
                $_POST['facebookId'],
                $_POST['facebookSecret'],
                $_POST['vimeoAccessToken']
            );
            $phinxConfigTemplate = file_get_contents($configPath . '/phinx.local-example.yml');
            if (!$phinxConfigTemplate) {
                throw new Exception('Cannot load phinx config template.');
            }
            if (!file_put_contents($configPath . '/phinx.local.yml', str_replace([
                    'host: db',
                    'name: dbjr',
                    'user: root',
                    'pass: pass',
                ], [
                    sprintf('host: %s', $_POST['dbHost']),
                    sprintf('name: %s', $_POST['dbName']),
                    sprintf('user: %s', $_POST['dbUsername']),
                    sprintf('pass: %s', $_POST['dbPass']),
                ], $phinxConfigTemplate))
            ) {
                throw new Exception('Cannot write phinx config.');
            }

            $phinxMigrate = new Service_PhinxMigrate('production');
            $phinxMigrate->run();

            $view->render('step-3.phtml');
        } catch (PDOException $e) {
            $form->getElement('dbHost')->addError('The specified db credentials are invalid.');
            $form->getElement('dbName')->addError('The specified db credentials are invalid.');
            $form->getElement('dbUsername')->addError('The specified db credentials are invalid.');
            $form->getElement('dbPass')->addError('The specified db credentials are invalid.');
        } catch (Exception $e) {
            $form->getElement('dbUsername')->addError('Cannot complete db initialization. Please check access rights.');
        }
    }
}

$form->populate(['locale' => $locale]);
$view->assign(['form' => $form->render($view->getZendView())]);
$view->render('step-2.phtml');
