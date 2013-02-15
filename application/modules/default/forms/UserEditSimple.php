<?php

class Default_Form_UserEditSimple extends Zend_Form {
  
  protected $_iniFile = '/modules/default/forms/UserEditSimple.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
  }
}
?>