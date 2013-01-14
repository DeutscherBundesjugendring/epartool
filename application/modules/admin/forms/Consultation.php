<?php
/**
 * Consultation
 *
 * @description   Form of consultation
 * @author        Markus Hackel
 */
class Admin_Form_Consultation extends Zend_Form {
  protected $_iniFile = '/application/modules/admin/forms/Consultation.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
    $consultationModel = new Consultations();
    $lastId = $consultationModel->getLastId();
    $highestId = $lastId + 1;
    $this->getElement('ord')->setDescription(
      '(z.B. höher=weiter vorn; z.B. neue höchste Konsultationsnummer: ' . $highestId . ')'
    );
    
    $this->getElement('inp_show')->setCheckedValue('y');
    $this->getElement('inp_show')->setUncheckedValue('n');
    $this->getElement('spprt_show')->setCheckedValue('y');
    $this->getElement('spprt_show')->setUncheckedValue('n');
    $this->getElement('vot_show')->setCheckedValue('y');
    $this->getElement('vot_show')->setUncheckedValue('n');
    $this->getElement('vot_res_show')->setCheckedValue('y');
    $this->getElement('vot_res_show')->setUncheckedValue('n');
    $this->getElement('summ_show')->setCheckedValue('y');
    $this->getElement('summ_show')->setUncheckedValue('n');
    $this->getElement('follup_show')->setCheckedValue('y');
    $this->getElement('follup_show')->setUncheckedValue('n');
    $this->getElement('public')->setCheckedValue('y');
    $this->getElement('public')->setUncheckedValue('n');
    
    $options = array(
        0 => 'keiner ausgewählt'
      );
    $userModel = new Users();
    $admins = $userModel->getAdmins();
    foreach ($admins as $admin) {
      $options[$admin->uid] = $admin->email;
    }
    $this->getElement('adm')->setMultioptions($options);
  }
}
