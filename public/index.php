<?php
// Applikationsverzeichnis festlegen
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

// Stufe festlegen
if (isset($_SERVER['DEVELOPER_MODE'])) {
  define('APPLICATION_ENVIRONMENT', 'development');
}
else {
  define('APPLICATION_ENVIRONMENT', 'production');
}

// Verzeichnisse für dem include_path definieren
$paths = array(
  APPLICATION_PATH . '/library', APPLICATION_PATH . '/application/models', '.'
);

// System konfigurieren
ini_set('include_path', implode(PATH_SEPARATOR, $paths));

// Error Reporting aktivieren, auf dem Produktionsserver sollten niemals
// Fehler ausgegeben werden
error_reporting(E_ALL | E_STRICT);

// lade die Zend_Loader Komponente und registriere die Autoload Methode
require_once("Zend/Loader/Autoloader.php");
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

// Bootstrap-Datei laden und starten
include APPLICATION_PATH . '/application/bootstrap.php';

// Front Controller ausführen
Zend_Controller_Front::getInstance()->dispatch();
