<?php
class Default_Bootstrap extends Zend_Application_Module_Bootstrap {
  /**
   * @todo Für die Verwendung des Messenger Plugins auch im Backend sollte diese
   * Methode in der Application Bootstrap Klasse definiert werden
   * Die entsprechenden Backend-Klassen und Viewskripte müssen dann entsprechend
   * angepasst werden
   *
   */
  public function _initMessenger() {
    $this->bootstrap('frontController');
    $this->getResource('frontController')->registerPlugin(new Plugin_Messenger());
  }
}
