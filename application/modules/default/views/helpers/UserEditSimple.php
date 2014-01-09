<?php

class Zend_View_Helper_UserEditSimple extends Zend_View_Helper_Abstract
{
    public function userEditSimple()
    {
        $html = '';
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $form = new Default_Form_UserEditSimple();
            $email = $form->getElement('email');
            $email->setValue($identity->email);

            $html.= $form->__toString();
        }

        return $html;
    }
}
