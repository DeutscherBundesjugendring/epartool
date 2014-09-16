<?php
class Admin_Form_Directory extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/Directory.ini';
    /**
     * Initialisieren des Formulars
     *
     */
    public function init()
    {
        // set form-config
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    }
}
