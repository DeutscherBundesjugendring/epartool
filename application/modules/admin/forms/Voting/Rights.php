<?php
/**
 * Voting_Rights
 *
 * @author        Markus Hackel
 */
class Admin_Form_Voting_Rights extends Zend_Form {
  protected $_iniFile = '/modules/admin/forms/Voting/Rights.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
  }
}