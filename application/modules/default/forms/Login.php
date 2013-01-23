<?php
/**
 * Login
 *
 * @description   Form of login for user
 * @author        Jan Suchandt
 */
class Default_Form_Login extends Zend_Form {
  protected $_iniFile = '/modules/default/forms/Login.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
  }
}
