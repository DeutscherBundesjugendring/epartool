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
  }
}
