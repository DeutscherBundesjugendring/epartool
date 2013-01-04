<?php
/**
 * Consultation
 *
 * @description   Form of consultation
 * @author        Markus Hackel
 */
class Admin_Form_Consultation extends Zend_Form {
  protected $_iniFile = '/application/modules/admin/forms/Consultation.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
  }
}
