<?php
/**
 * E-Mail-Template
 *
 * @description   Form new email to send
 * @author        Jan Suchandt
 */
class Admin_Form_Email_Template extends Zend_Form {
  protected $_iniFile = '/modules/admin/forms/Email/Template.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
    $templateModel = new Model_Emails_Templates();
    $this->getElement('refnm')->setMultioptions(
      $templateModel->getAllReferences()
    );
    
    $consultationModel = new Model_Consultations();
    $consultations = $consultationModel->getAll()->toArray();
    $consultationsArr = array(
      0=>'Grundsätzliche E-Mail-Vorlage'
    );
    foreach($consultations AS $val) {
      $consultationsArr[$val['kid']] = $val['titl'];
    }
    $this->getElement('kid')->setMultioptions($consultationsArr);
  }
}
