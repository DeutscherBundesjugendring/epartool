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
//     $password = $this->getElement('register_password');
//     $password->getValidator('StringLength')
//       ->setMin(6)
//       ->setMessage('Ihr Kennwort ist zu kurz.', 'stringLengthTooShort');
      
    $group = $this->getElement('group_type');
    $group->removeDecorator('Label')
      // set default:
      ->setValue('single');
    
    $systemconfig = Zend_Registry::get('systemconfig');
    $grp_siz_def = $systemconfig->group_size_def->toArray();
    unset($grp_siz_def['0']);
    unset($grp_siz_def['1']);
    
    // subform for group_type == "group"
    $groupSpecs = new Zend_Form_SubForm();
    $groupSpecs->addElements(array(
      $this->getElement('source'),
      $this->getElement('src_misc'),
      $this->getElement('group_size')->setMultioptions($grp_siz_def),
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
      . '    $(\'select#age_group option\').filter("[value=\'4\']").remove();' . "\n"
      . '  }' . "\n"
      . '  $(\'input[name="group_type"]\').change(function() {' . "\n"
      . '    groupTypeChecked = $(\'input[name="group_type"]:checked\').val();' . "\n"
      . '    if (groupTypeChecked == "group") {' . "\n"
      . '      container.slideDown();' . "\n"
      . '      $(\'select#age_group\').append($(\'<option></option>\').val(\'4\').html(\'Alle Altersgruppen\'));' . "\n"
      . '    } else {' . "\n"
      . '      container.slideUp();' . "\n"
      . '      $(\'select#age_group option\').filter("[value=\'4\']").remove();' . "\n"
      . '    }' . "\n"
      . '  });' . "\n"
      . '});' . "\n"
      . '</script>' . "\n";
    $script->setDescription($code);
  }
}
