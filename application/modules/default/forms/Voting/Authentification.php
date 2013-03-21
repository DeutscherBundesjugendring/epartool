<?php
/**
 * Voting Authenification
 * Formular zur Berechtigung zur Abstimmung
 *
 */
class Default_Form_Voting_Authentification extends Zend_Form {
  protected $_iniFile = '/modules/default/forms/Voting/Authentification.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
  }
}