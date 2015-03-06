<?php

class Zend_View_Helper_Login extends Zend_View_Helper_Abstract
{
    public function login()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $webserviceLoginSess = new Zend_Session_Namespace('webserviceLoginCsrf');
            if (Zend_Registry::get('systemconfig')->webservice) {
                $webserviceLoginSess->csrf = sha1(rand(0, 100) . time());
            } else {
                $webserviceLoginSess->csrf = null;
            }

            return $this->view->partial(
                '_helpers/login.phtml',
                [
                    'form' => new Default_Form_Login(),
                    'webserviceLoginCsrf' => $webserviceLoginSess->csrf,
                ]
            );
        }
    }
}
