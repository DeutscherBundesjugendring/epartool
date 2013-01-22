<?php
/**
 * Plugin, das Aktionen hinzufügt
 *
 */
class Application_Controller_Plugin_ActionSetup extends Zend_Controller_Plugin_Abstract {
  
  /**
   * Wird vor der Dispatcher Schleife aufgerufen
   *
   * @param  Zend_Controller_Request_Abstract $request
   * @return void
   */
  public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
    
  }
}
?>