<?php

class Default_Form_PasswordRecover extends Zend_Form {
  
  protected $_iniFile = '/modules/default/forms/PasswordRecover.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
    $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/user/passwordrecover');
    
    // CSRF Protection
    $hash = $this->createElement('hash', 'csrf_token_pwrecover',
        array('salt' => 'unique'));
    $hash->setSalt(md5(mt_rand(1, 100000) . time()));
    $this->addElement($hash);
  }
}
?>