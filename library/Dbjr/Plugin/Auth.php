<?php
/**
 * Plugin, das die Athentifizierung prüft
 *
 */
class Dbjr_Plugin_Auth extends Zend_Controller_Plugin_Abstract {
  
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