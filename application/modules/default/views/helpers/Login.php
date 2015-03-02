<?php

class Zend_View_Helper_Login extends Zend_View_Helper_Abstract
{
    public function login()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            return $this->view->partial(
                '_helpers/login.phtml',
                ['form' => new Default_Form_Login()]
            );
        }
    }
}
