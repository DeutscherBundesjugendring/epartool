<?php
class Admin_Form_Tag extends Zend_Form {
  protected $_iniFile = '/modules/admin/forms/Tag.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    $this->addPrefixPath('Dbjr_Form', 'Dbjr/Form/');
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
    $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/tag/create');
    
    // CSRF Protection
    $hash = $this->createElement('hash', 'csrf_token_tagadmin', array('salt' => 'unique'));
    $hash->setSalt(md5(mt_rand(1, 100000) . time()));
    if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
      $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
    }
    $this->addElement($hash);
  }
}
?>