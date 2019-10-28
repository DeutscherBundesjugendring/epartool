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
set_time_limit(0);

$rootDir = realpath(dirname(__DIR__));
$locale = !empty($_GET['locale']) ? $_GET['locale'] : null;


$view = new View(
    realpath(__DIR__ . '/views/layouts'),
    realpath(__DIR__ . '/views/scripts'),
    $rootDir . '/application/views/helpers',
    $rootDir . '/vendor/shardj/zf1-future/resources/languages',
    realpath(__DIR__ . '/languages'),
    $locale
);

const RUNTIME_FOLDERS = [
    'runtime/logs',
    'runtime/sessions',
    'runtime/cache',
    'www/image_cache',
    'www/media/consultations',
    'www/media/folders',
];
const SOURCE_FOLDERS = [
    'application/configs'
];
$allFolders = array_merge(RUNTIME_FOLDERS, SOURCE_FOLDERS);


$sqlPath = $rootDir . '/data';
$fileSystem = new FileSystem($rootDir);
$configPath = $rootDir . '/application/configs';

if (!isset($locale)) {
    $view->render('step-1.phtml');
} elseif (file_exists($configPath . '/config.local.ini')) {
    $view->render('already-installed.phtml');
} elseif (!$fileSystem->validateWritable($allFolders)) {
    $fileSystem->createFolders(RUNTIME_FOLDERS);
    $nonWritableFolders = $fileSystem->getNonWritableFolders($allFolders);
    if ($nonWritableFolders) {
        $view->assign([
            'allFolders' => $allFolders,
            'locale' => $locale,
            'nonWritableFolders' => $nonWritableFolders,
        ]);
        $view->render('file-permissions.phtml');
    }
}


$form = new InstallForm();
if (!empty($_POST) && $form->isValid($_POST)) {
    try {
        $db = new Db($_POST['dbName'], $_POST['dbHost'], $_POST['dbUsername'], $_POST['dbPass']);

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

        $consultationId = $db->initDb(
            $sqlPath,
            $_POST['adminName'],
            $_POST['adminEmail'],
            $_POST['adminPassword'],
            $_POST['locale'],
            static function () use ($phinxMigrate) {
                $phinxMigrate->run();
            }
        );
        $fileSystem->createFolders(['/www/media/consultations/' . $consultationId]);

        $config = new Config(new Zend_Config_Writer_Ini(), $configPath);
        $baseUrl = preg_replace(
            sprintf('#^%s#', $_SERVER['DOCUMENT_ROOT']),
            '',
            realpath($rootDir)
        );
        $config->writeConfigLocalIni(
            $baseUrl === '' ? '/' : $baseUrl,
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
            mb_substr(hash('sha256', random_bytes(20)), 32),
            $_POST['googleId'],
            $_POST['googleSecret'],
            $_POST['facebookId'],
            $_POST['facebookSecret'],
            $_POST['vimeoAccessToken']
        );

        $frontController = Zend_Controller_Front::getInstance();
        $frontController->setBaseUrl($baseUrl);
        $frontController->getRouter()->addDefaultRoutes();
        $view->render('step-3.phtml');
    } catch (PDOException $e) {
        $form->getElement('dbHost')->addError('The specified db credentials are invalid.');
        $form->getElement('dbName')->addError('The specified db credentials are invalid.');
        $form->getElement('dbUsername')->addError('The specified db credentials are invalid.');
        $form->getElement('dbPass')->addError('The specified db credentials are invalid.');
    } catch (Exception $e) {
        $form->getElement('dbUsername')->addError('Something went wrong, we could not create the installation.');
    }
}

$form->populate(['locale' => $locale]);
$view->assign(['form' => $form->render($view->getZendView())]);
$view->render('step-2.phtml');
