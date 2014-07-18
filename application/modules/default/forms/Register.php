<?php
/**
 * Register Form
 *
 */
class Default_Form_Register extends Zend_Form
{
    protected $_iniFile = '/modules/default/forms/Register.ini';
    /**
     * Initialisieren des Formulars
     *
     */
    public function init()
    {
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/user/register');

        $group = $this->getElement('group_type');
        $group
            ->removeDecorator('Label')
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

        $validator = new Zend_Validate_InArray(array(1));
        $validator->setMessage('You must agree');
        $this->getElement('is_contrib_under_cc')->setDisableTranslator('true');
        $this->getElement('is_contrib_under_cc')->addValidator($validator);
        $this->getElement('is_contrib_under_cc')->getDecorator('Label')->setOptions(array('escape' => false));
        $this->getElement('is_contrib_under_cc')->setLabel(
            sprintf(
                Zend_Registry::get('Zend_Translate')->translate(
                    'Die Beiträge werden unter einer <a href="%s" target="_blank" title="Mehr über die Creative-Commons-Lizenz erfahren">Creative-Commons-Lizenz</a> veröffentlicht. Das bedeutet, dass eure Beiträge nicht-kommerziell in Zusammenfassungen und Publikationen weiterverwendet werden dürfen. Da alle Beiträge hier anonym veröffentlicht werden, wird auch bei Weiterverwendung als Quelle nur diese Website genannt werden.'
                ),
                Zend_Registry::get('systemconfig')->license->creative_commons->link
            )
        );

        // add javascript for toggling subform
        $script = $this->getElement('script');
        $code = '<script type="text/javascript">' . "\n"
            . '$(document).ready(function () {' . "\n"
            . '    var container = $("#group_specs-element");' . "\n"
            . '    var labelName = $("#name-label");' . "\n"
            . '    var elementName = $("#name-element");' . "\n"
            . '    var groupTypeChecked = $(\'input[name="group_type"]:checked\').val();' . "\n"
            . '    if (groupTypeChecked != "group") {' . "\n"
            . '        labelName.show();' . "\n"
            . '        elementName.show();' . "\n"
            . '        container.hide();' . "\n"
            . '        $(\'select#age_group option\').filter("[value=\'4\']").remove();' . "\n"
            . '    }' . "\n"
            . '    $(\'input[name="group_type"]\').change(function () {' . "\n"
            . '        groupTypeChecked = $(\'input[name="group_type"]:checked\').val();' . "\n"
            . '        if (groupTypeChecked == "group") {' . "\n"
            . '            labelName.hide();' . "\n"
            . '            elementName.hide();' . "\n"
            . '            container.slideDown();' . "\n"
            . '            $(\'select#age_group\').append($(\'<option></option>\').val(\'4\').html(\'Alle Altersgruppen\'));' . "\n"
            . '        } else {' . "\n"
            . '            labelName.show();' . "\n"
            . '            elementName.show();' . "\n"
            . '            container.slideUp();' . "\n"
            . '            $(\'select#age_group option\').filter("[value=\'4\']").remove();' . "\n"
            . '        }' . "\n"
            . '    });' . "\n"
            . '});' . "\n"
            . '</script>' . "\n";
        $script->setDescription($code);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_register', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }

    /**
     * Makes the email field element disabled, but adds a hidden field so the value still gets submitted
     */
    public function lockEmailField() {
        $emailDisabledEl = $this
            ->getElement('email')
            ->setName('email-disabled')
            ->setOrder(1)
            ->setAttrib('disabled', 'disabled');

        $emailHiddenEl = $this
            ->createElement('hidden', 'email')
            ->setValue($emailDisabledEl->getValue())
            ->setName('email');
        $this->addElement($emailDisabledEl);
        $this->addElement($emailHiddenEl);
    }
}
