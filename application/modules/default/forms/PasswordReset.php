<?php

class Default_Form_PasswordReset extends Zend_Form
{
    protected $_iniFile = '/modules/default/forms/UserEditSimple.ini';

    public function init()
    {
        $this->addElement(
            $this
                ->createElement('password', 'password')
                ->setLabel('Password')
                ->setRequired(true)
                ->setAttrib('class', 'has-password-meter')
                ->addValidator('stringLength', 'min', Zend_Registry::get('systemconfig')->security->password->minLength)
        );

        $this->addElement(
            $this
                ->createElement('password', 'password_confirm')
                ->setLabel('Password confirmation')
                ->setRequired(true)
                ->addValidator('identical', true, 'password')
        );

        $this->addElement(
            $this
                ->createElement('button', 'submit')
                ->setLabel('Reset')
                ->setAttrib('type', 'submit')
        );

        $this->addElement(
            $this
                ->createElement('hash', 'csrf_token', array('salt' => 'unique'))
                ->setSalt(md5(mt_rand(1, 100000) . time()))
                ->setTimeout(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl)
        );
    }
}
