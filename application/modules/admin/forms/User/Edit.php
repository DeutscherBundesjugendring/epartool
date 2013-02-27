<?php
/**
 * User Edit
 *
 * @description   Form for User Edit
 * @author        Jan Suchandt
 */
class Admin_Form_User_Edit extends Zend_Form {
  protected $_iniFile = '/modules/admin/forms/User/Edit.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
    $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/user/edit');
    
    // JSU options für select-feld setzen
    $options = array(
      'usr'=>'Benutzer',
      'edt'=>'Redakteur',
      'adm'=>'Administrator',
    );
    $this->getElement('lvl')->setMultioptions($options);
    
    $options = array(
      'y'=>'Ja',
      'n'=>'Nein'
    );
    $this->getElement('newsl_subscr')->setMultioptions($options);
    
    $options = array(
      'b'=>'Blockiert',
      'u'=>'Unbestätigt',
      'c'=>'Bestätigt'
    );
    $this->getElement('block')->setMultioptions($options);
  }
}
