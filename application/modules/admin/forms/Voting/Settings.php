<?php
/**
 * Consultation
 *
 * @description   Form of consultation
 * @author        Karsten Tackmann
 */
class Admin_Form_Voting_Settings extends Zend_Form {
  protected $_iniFile = '/modules/admin/forms/Voting/Settings.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
   $this->getElement('vot_show')->setCheckedValue('y');
   $this->getElement('vot_show')->setUncheckedValue('n');
   $this->getElement('vot_res_show')->setCheckedValue('y');
   $this->getElement('vot_res_show')->setUncheckedValue('n');
    
  }
}