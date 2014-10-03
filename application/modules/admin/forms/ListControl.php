<?php

class Admin_Form_ListControl extends Dbjr_Form_Admin
{

    public function init()
    {
        $this->setMethod('post');

        $hash = $this->createElement('hash', 'list_control_csrf_token', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);
    }
}
