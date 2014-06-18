<?php

class Admin_Form_Media_Delete extends Zend_Form
{
    protected $_iniFile = '/modules/admin/forms/Media/Delete.ini';

    public function init()
    {
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));
    }

    /**
     * Adds csrf hash element. If there are more then one form on the same page, the elements must have different names
     * @param string $elementName The name of the csrf hash element
     */
    public function addCsrfHash($elementName)
    {
        $hash = $this->createElement('hash', $elementName, array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);
    }
}
