<?php

/**
 * Description of Followup
 *
 * @author Marco Dinnbier
 */
class Admin_Form_Followup_File extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/Followup/File.ini';
    /**
     * Initialisieren des Formulars
     *
     */
    public function init()
    {
        // set form-config
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        $this->getElement('ref_doc')->addPrefixPath('Admin_Form_Decorator', 'Admin/Form/Decorator', 'decorator');
        $this->getElement('gfx_who')->addPrefixPath('Admin_Form_Decorator', 'Admin/Form/Decorator', 'decorator');

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $kid = $request->getParam('kid', 0);
        $this->getElement('ref_doc')->setAttrib('id', 'ref_doc');
        $this->getElement('gfx_who')->setAttrib('id', 'gfx_who');
        $formid = $this->getAttrib('id');
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

        $urlparams = "?formid=$formid&elemid=".$this->getElement('ref_doc')->getAttrib('id');
        $this->getElement('ref_doc')->setDecorators(
            array(
                'Label',
                'ViewHelper',
                array(
                    'Popuplink',
                    array(
                        'url' => $baseUrl . '/admin/media/choose/kid/' . $kid . $urlparams,
                        'text' => 'Datei wählen'
                    )
                )
            )
        );

        $urlparams = "?upload_scenario=" . Model_FollowupFiles::UPLOAD_SCENARIO_THUMB . "&formid=$formid&elemid=".$this->getElement('gfx_who')->getAttrib('id');
        $this->getElement('gfx_who')->setDecorators(
            array(
                'Label',
                'ViewHelper',
                array(
                    'Popuplink',
                    array(
                        'url' => $baseUrl . '/admin/media/choose/kid/' . $kid . $urlparams,
                        'text' => 'Datei wählen'
                    )
                )
            )
        );

    }
}
