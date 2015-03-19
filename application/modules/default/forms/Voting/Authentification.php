<?php

class Default_Form_Voting_Authentification extends Dbjr_Form_Web
{
    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $this
            ->setMethod('post')
            ->setAttrib('class', 'form-inline')
            ->setAttrib('autocomplete', 'off');

        $email = $this->createElement('email', 'email');
        $placeholder = $translator->translate('Email Address');
        $email
            ->setLabel('Email Address')
            ->setRequired(true)
            ->setAttrib('placeholder', $placeholder)
            ->setValidators([['NotEmpty', true], 'EmailAddress']);
        $this->addElement($email);

        $code = $this->createElement('text', 'authcode');
        $placeholder = $translator->translate('Access code');
        $code
            ->setLabel('Your access code')
            ->setRequired(true)
            ->setAttrib('placeholder', $placeholder)
            ->setValidators(['NotEmpty']);
        $this->addElement($code);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary')
            ->setLabel('Start');
        $this->addElement($submit);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_votingauth', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }
}
