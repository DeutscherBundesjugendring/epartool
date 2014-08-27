<?php

class Default_Form_PasswordRecover extends Zend_Form
{

    public function init()
    {
        $view = new Zend_View();

        $this
            ->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/user/passwordrecover')
            ->setMethod('post');

        $email = $this->createElement('text', 'email');
        $placeholder = $view->translate('Email Address');
        $email
            ->setLabel('Email Address')
            ->setRequired(true)
            ->setAttrib('placeholder', $placeholder)
            ->setValidators([['NotEmpty', true], 'EmailAddress']);
        $this->addElement($email);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setLabel('Send')
            ->setAttrib('class', 'btn');
        $this->addElement($submit);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_pwrecover', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }
}
