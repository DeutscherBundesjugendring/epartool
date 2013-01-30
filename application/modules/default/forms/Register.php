<?php
/**
 * Register Form
 *
 */
class Default_Form_Register extends Zend_Form {
  protected $_iniFile = '/modules/default/forms/Register.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
    // aus bisher unerfindlichen Gründen funktioniert folgende Konfiguration
    // des Passwortfeldes nicht per INI:
    $password = $this->getElement('register_password');
    $password->getValidator('StringLength')
      ->setMin(6)
      ->setMessage('Ihr Kennwort ist zu kurz.', 'stringLengthTooShort');
    
    $this->getElement('newsl_subscr')
      ->setCheckedValue('y')
      ->setUncheckedValue('n');
    
    $kid = $this->getElement('kid');
    // hidden, deshalb nur ViewHelper Decorator:
    $kid->setDecorators(array('ViewHelper'));
  }
}
