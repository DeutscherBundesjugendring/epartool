<?php

class Default_Form_PasswordRecover extends Dbjr_Form_Web
{

    public function init()
    {
        $this
            ->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/user/passwordrecover')
            ->setMethod('post');

        $email = $this->createElement('email', 'email');
        $placeholder = Zend_Registry::get('Zend_Translate')->translate('Email Address');
        $email
            ->setLabel('Email Address')
            ->setRequired(true)
            ->setAttrib('placeholder', $placeholder)
            ->setValidators([['NotEmpty', true], 'EmailAddress']);
        $this->addElement($email);

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Send');
        $this->addElement($submit);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_pwrecover', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }
}
