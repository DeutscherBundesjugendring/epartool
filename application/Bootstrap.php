<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
  
  protected function _initConfig() {
    // Lade Konfiguration
    $config = new Zend_Config_Ini(
        APPLICATION_PATH . '/modules/default/config/config.ini',
        APPLICATION_ENV);
    
    // Speichere Konfiguration in der Registry
    Zend_Registry::set('config', $config);
  }
  
  protected function _initDefaultModuleAutoloader() {
    $resourceLoader = new Zend_Application_Module_Autoloader(array(
        'namespace' => '',
        'basePath'  => APPLICATION_PATH,
    ));

    return $resourceLoader;
  }
  
  protected function _initRegistry() {
    // Initialisierung des Db-Adapters erzwingen
    $this->bootstrap('db');
    $registry = Zend_Registry::getInstance();
    $config = new Zend_Config($this->getOptions());
    $registry->configuration = $config;
//    Zend_Debug::dump($registry->configuration);
    $registry->dbAdapter = $this->getResource('db');
    return $registry;
  }
  
  protected function _initAuth() {
    $this->bootstrap('frontController');
    $auth = Zend_Auth::getInstance();
    $acl = new Plugin_Auth_Acl();
    $this->getResource('frontController')
      ->registerPlugin(new Plugin_Auth_AccessControl($auth, $acl))
      ->setParam('auth', $auth);
  }
}
?>