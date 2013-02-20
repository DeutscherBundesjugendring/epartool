<?php
/**
 * Edit Form
 * Formular für Beiträge zu Fragen
 *
 */
class Default_Form_Input_Edit extends Zend_Form {
  protected $_iniFile = '/modules/default/forms/Input/Edit.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
    $this->setDecorators(array('FormElements', 'Form'));
    
    // für alle per ini gesetzten Elemente:
    // nur die Dekoratoren ViewHelper, Errors und Description verwenden
    $this->setElementDecorators(array('ViewHelper', 'Errors', 'Description'));
  }
}