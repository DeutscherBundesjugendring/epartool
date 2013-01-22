<?php
/**
 * Configs
 *
 * @description   Model f�r Konfigurationsgruppen
 * @author        Jan Suchandt
 */
class Form_ConfigsEdit extends Zend_Form {
  protected $_iniFile = '/forms/Login.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // Yaml-Dekoratoren laden
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
  }
}
