<?php
class Admin_Form_Tag extends Zend_Form {
  protected $_iniFile = '/modules/admin/forms/Tag.ini';
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