<?php

class Admin_Form_Followup_Snippet extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/Followup/Snippet.ini';

    public function init()
    {
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        $this->getElement('typ')
            ->addMultioptions(Model_Followups::getTypes())
            ->setValue('g');

        $this->getElement('expl')->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD);
    }
}
