<?php

class Admin_Form_ArticlePreview extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/ArticlePreview.ini';

    public function init()
    {
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->setDecorators(array('ViewHelper'));
       }
    }
}
