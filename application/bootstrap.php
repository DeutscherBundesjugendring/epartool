<?php
// Lade Konfiguration
$config = new Zend_Config_Ini(
    APPLICATION_PATH . '/application/modules/default/config/config.ini',
    APPLICATION_ENVIRONMENT);

// Speichere Konfiguration in der Registry
Zend_Registry::set('config', $config);

// hole Instanz des Front Controllers
$frontController = Zend_Controller_Front::getInstance();

// lege Controller Verzeichnisse fest
//$frontController
//    ->setControllerDirectory(APPLICATION_PATH . '/application/controllers');
$frontController->addModuleDirectory(APPLICATION_PATH . '/application/modules');

// Übergebe das Applikationsverzeichnis an den Controller
$frontController->setParam('root', APPLICATION_PATH);

// Starte MVC für Zend_Layout
$layout = Zend_Layout::startMvc(
    APPLICATION_PATH . '/application/layouts/scripts/');
// Frontend als Standard-Layout verwenden (Layout für Backend wird im AdminController gesetzt [init])
$layout->setLayout('frontend');

// Erstelle Datenbankadapter und speichere in der Registry
Zend_Registry::set('db', Zend_Db::factory($config->db));
Zend_Db_Table_Abstract::setDefaultAdapter(Zend_Registry::get('db'));

// Formloader
$loader = new Zend_Loader_PluginLoader();
$loader->addPrefixPath('Form', APPLICATION_PATH . '/application/modules/default/forms/');
$loader->addPrefixPath('Admin_Form', APPLICATION_PATH . '/application/modules/admin/forms/');
Zend_Registry::set('formloader', $loader);
// Pfade
Zend_Registry::set('path', $config->path);

// Variablen aufrÃ¤umen
unset($frontController, $config);
