<?php
/**
 * Question
 *
 * @description   Form of question
 * @author        Markus Hackel
 */
class Admin_Form_Question extends Zend_Form {
  protected $_iniFile = '/application/modules/admin/forms/Question.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
  }
}
