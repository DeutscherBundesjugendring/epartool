<?php

class Admin_Form_Input extends Dbjr_Form_Admin
{
    protected $_iniFile = '/modules/admin/forms/Input.ini';

    public function init()
    {
        $this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . $this->_iniFile));

        $questionModel = new Model_Questions();
        $kid = Zend_Controller_Front::getInstance()->getRequest()->getParam('kid', 0);
        $selectOptions = $questionModel->getAdminInputFormSelectOptions($kid);
        $this->getElement('qi')->setMultiOptions($selectOptions);

        $tagModel = new Model_Tags();
        $multiOptions = $tagModel->getAdminInputFormMulticheckboxOptions();
        if (!empty($multiOptions)) {
            $tags = $this->getElement('tags');
            $tags->setMultiOptions($multiOptions);
            $htmlTag = $tags->getDecorator('HtmlTag');
            $htmlTag->setOption('class', 'multicheckbox');
            $tags->setSeparator(' ');
        } else {
            $this->removeElement('tags');
        }

        // CSRF Protection
        $hash = $this->getHash();
        $this->addElement($hash);
    }

	 public function getHash() {
	 	$hash = $this->createElement('hash', 'csrf_token_inputadmin', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
		return $hash;
	 }
}
