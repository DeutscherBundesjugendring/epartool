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
    
    $projectModel = new Model_Projects();
    $projects = $projectModel->getAll();
    $options = array();
    foreach ($projects as $project) {
      $options[$project['proj']] = $project['titl_short'];
    }
    $this->getElement('proj')->setMultiOptions($options);
    // current project has to be checked always:
    $this->getElement('proj')->setValue(array(Zend_Registry::get('systemconfig')->project));
    
    // CSRF Protection
    $hash = $this->createElement('hash', 'csrf_token_mailtemplate', array('salt' => 'unique'));
    $hash->setSalt(md5(mt_rand(1, 100000) . time()));
    if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
      $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
    }
    $this->addElement($hash);
  }
}
