<?php

class Admin_Form_ListControl extends Dbjr_Form_Admin
{
    /**
     * Holds the name of the csrf token element
     */
    const CSRF_TOKEN_NAME = 'list_control_csrf_token';

    public function init()
    {
        $hash = $this->createElement('hash', self::CSRF_TOKEN_NAME, array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);
    }

    /**
     * Returns the name of the csrf token element
     * @return string  The name of the csrf token element
     */
    public function getCsrfTokenName()
    {
        return self::CSRF_TOKEN_NAME;
    }
}
