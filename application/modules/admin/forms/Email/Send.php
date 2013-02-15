<?php
/**
 * Email to send
 *
 * @description   Form new email to send
 * @author        Jan Suchandt
 */
class Admin_Form_Email_Send extends Zend_Form {
  protected $_iniFile = '/modules/admin/forms/Email/Send.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
  }
}
