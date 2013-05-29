<?php
/**
 * Article
 *
 * @description   Form of Article
 * @author        Markus Hackel
 */
class Admin_Form_Article extends Zend_Form {
  protected $_iniFile = '/modules/admin/forms/Article.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
    $options = array(
      0 => 'Bitte auswählen...',
    );

    $this->getElement('ref_nm')->setMultioptions($options);
    
    $this->getElement('hid')->setCheckedValue('y');
    $this->getElement('hid')->setUncheckedValue('n');
    
    $projectModel = new Model_Projects();
    $projects = $projectModel->getAll();
    $options = array();
    foreach ($projects as $project) {
      $options[$project['proj']] = $project['titl_short'];
    }
    $this->getElement('proj')->setMultiOptions($options);
    // current project has to be checked always:
    $this->getElement('proj')->setValue(array(Zend_Registry::get('systemconfig')->project));
  }
}
