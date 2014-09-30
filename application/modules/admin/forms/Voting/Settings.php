<?php

class Admin_Form_Voting_Settings extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/Voting/Settings.ini';

    public function init()
    {
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        $this->getElement('vot_show')->setCheckedValue('y');
        $this->getElement('vot_show')->setUncheckedValue('n');
        $this->getElement('vot_res_show')->setCheckedValue('y');
        $this->getElement('vot_res_show')->setUncheckedValue('n');
        $this->getElement('vot_expl')->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD);

        $this->getElement('vot_fr')->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME);
        $this->getElement('vot_to')->setDatepicker(Dbjr_Form_Element_Text::DATEPICKER_TYPE_DATETIME);
    }
}
