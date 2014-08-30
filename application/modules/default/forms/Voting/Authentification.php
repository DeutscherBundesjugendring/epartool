<?php

class Default_Form_Voting_Authentification extends Zend_Form
{
    public function init()
    {
        $view = new Zend_View();

        $this
            ->setMethod('post')
            ->setAttrib('autocomplete', 'off');

        $email = $this->createElement('text', 'email');
        $placeholder = $view->translate('Email Address');
        $email
            ->setLabel('Email Address')
            ->setRequired(true)
            ->setAttrib('placeholder', $placeholder)
            ->setValidators([['NotEmpty', true], 'EmailAddress']);
        $this->addElement($email);

        $code = $this->createElement('text', 'authcode');
        $placeholder = $view->translate('Access code');
        $code
            ->setLabel('Your access code')
            ->setRequired(true)
            ->setAttrib('placeholder', $placeholder)
            ->setValidators(['NotEmpty']);
        $this->addElement($code);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setLabel('Start')
            ->setAttrib('class', 'btn');
        $this->addElement($submit);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_votingauth', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }
}
