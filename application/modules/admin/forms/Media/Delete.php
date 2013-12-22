<?php
/**
 * Media Delete
 *
 * @description   Form for Media deletion
 * @author        Markus Hackel
 */
class Admin_Form_Media_Delete extends Zend_Form {
  protected $_iniFile = '/modules/admin/forms/Media/Delete.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
    // CSRF Protection
    $hash = $this->createElement('hash', 'csrf_token_mediadelete', array('salt' => 'unique'));
    $hash->setSalt(md5(mt_rand(1, 100000) . time()));
    if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
      $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
    }
    $this->addElement($hash);
  }
}
