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
}
?>