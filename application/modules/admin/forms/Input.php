<?php
/**
 * Input
 *
 * @description   Form of Input
 * @author        Markus Hackel
 */
class Admin_Form_Input extends Zend_Form {
  protected $_iniFile = '/modules/admin/forms/Input.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
    // Select für Fragezuordnung:
    $questionModel = new Model_Questions();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $kid = $request->getParam('kid', 0);
    $selectOptions = $questionModel->getAdminInputFormSelectOptions($kid);
    $this->getElement('qi')->setMultiOptions($selectOptions);
    
    // Multicheckbox für Tags
    $tagModel = new Model_Tags();
    $multiOptions = $tagModel->getAdminInputFormMulticheckboxOptions();
    if (!empty($multiOptions)) {
      $tags = $this->getElement('tags');
      $tags->setMultiOptions($multiOptions);
      $htmlTag = $tags->getDecorator('HtmlTag');
      // css Klasse für individuelles Styling im Decorator 'HtmlTag' setzen:
      $htmlTag->setOption('class', 'multicheckbox');
    } else {
      // falls keine Tags definiert
      $this->removeElement('tags');
    }
  }
}
