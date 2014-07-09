<?php

class Admin_Form_ListControl extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/ListControl.ini';

    public function init()
    {
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    }
}
