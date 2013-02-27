<?php
/**
 * Register Form
 *
 */
class Default_Form_Register extends Zend_Form {
  protected $_iniFile = '/modules/default/forms/Register.ini';
  /**
   * Initialisieren des Formulars
   *
   */
  public function init() {
    // set form-config
    $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    
    $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/user/register');
    
    // set options for stringlength validator
    $password = $this->getElement('register_password');
    $password->getValidator('StringLength')
      ->setMin(6)
      ->setMessage('Ihr Kennwort ist zu kurz.', 'stringLengthTooShort');
      
    $group = $this->getElement('group_type');
    $group->removeDecorator('Label');
    
    // subform for group_type == "group"
    $groupSpecs = new Zend_Form_SubForm();
    $groupSpecs->addElements(array(
      $this->getElement('source'),
      $this->getElement('src_misc'),
      $this->getElement('group_size'),
      $this->getElement('name_group'),
      $this->getElement('name_pers'),
    ));
    // remove these elements from original form
    $this->removeElement('source');
    $this->removeElement('src_misc');
    $this->removeElement('group_size');
    $this->removeElement('name_group');
    $this->removeElement('name_pers');
    $this->addSubForm($groupSpecs, 'group_specs', 6);
    
    // add javascript for toggling subform
    $script = $this->getElement('script');
    $code = '<script type="text/javascript">' . "\n"
      . '$(document).ready(function() {' . "\n"
      . '  var container = $("#group_specs-element");' . "\n"
      . '  var groupTypeChecked = $(\'input[name="group_type"]:checked\').val();' . "\n"
      . '  if (groupTypeChecked != "group") {' . "\n"
      . '    container.hide();' . "\n"
      . '  }' . "\n"
      . '  $(\'input[name="group_type"]\').change(function() {' . "\n"
      . '    groupTypeChecked = $(\'input[name="group_type"]:checked\').val();' . "\n"
      . '    if (groupTypeChecked == "group") {' . "\n"
      . '      container.slideDown();' . "\n"
      . '    } else {' . "\n"
      . '      container.slideUp();' . "\n"
      . '    }' . "\n"
      . '  });' . "\n"
      . '});' . "\n"
      . '</script>' . "\n";
    $script->setDescription($code);
  }
}
