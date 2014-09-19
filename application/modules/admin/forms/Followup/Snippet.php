<?php

/**
 * Description of Followup
 *
 * @author Marco Dinnbier
 */
class Admin_Form_Followup_Snippet extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/Followup/Snippet.ini';
    /**
     * Initialisieren des Formulars
     *
     */
    public function init()
    {
        // set form-config
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
        $this->getElement('typ')->addMultioptions(array('g' => 'general', 's' => 'supporting', 'a' => 'action','r' => 'rejected','e' => 'end'));
        $this->getElement('typ')->setValue('g');

        $this->getElement('expl')->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD);
    }
}
