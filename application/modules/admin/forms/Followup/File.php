<?php

class Admin_Form_Followup_File extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/Followup/File.ini';

    public function setKid($kid)
    {
        $this->getElement('ref_doc')
            ->setKid($kid)
            ->setIsLockDir(true);
        $this->getElement('gfx_who')
            ->setKid($kid)
            ->setIsLockDir(true);
    }

    public function init()
    {
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        $this->getElement('ref_doc')->addPrefixPath('Admin_Form_Decorator', 'Admin/Form/Decorator', 'decorator');
        $this->getElement('gfx_who')->addPrefixPath('Admin_Form_Decorator', 'Admin/Form/Decorator', 'decorator');

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $kid = $request->getParam('kid', 0);
        $this->getElement('ref_doc')->setAttrib('id', 'ref_doc');
        $this->getElement('gfx_who')->setAttrib('id', 'gfx_who');
    }
}
