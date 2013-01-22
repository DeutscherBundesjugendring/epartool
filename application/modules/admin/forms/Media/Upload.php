<?php
/**
 * Media Upload
 *
 * @description   Form for Media upload
 * @author        Markus Hackel
 */
class Admin_Form_Media_Upload extends Zend_Form {
  protected $_iniFile = '/modules/admin/forms/Media/Upload.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
  }
}
