<?php

/**
 * Description of Followup
 *
 * @author Marco Dinnbier
 */
class Admin_Form_Followup_Snippet extends Zend_Form {
  protected $_iniFile = '/modules/admin/forms/Followup/Snippet.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));    
    $this->getElement('typ')->addMultioptions(array('g' => 'general', 'a' => 'action','r' => 'rejected','e' => 'end'));
    $this->getElement('typ')->setValue('g');
            
  }
}

?>
