<?php

class Default_Form_Voting_Authentification extends Dbjr_Form_Web
{
    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $this
            ->setMethod('post')
            ->setAttrib('class', 'form-inline offset-bottom-large')
            ->setAttrib('autocomplete', 'off');

        $email = $this->createElement('email', 'email');
        $placeholder = $translator->translate('Email Address');
        $email
            ->setLabel('Email Address')
            ->setRequired(true)
            ->setAttrib('placeholder', $placeholder)
            ->setAttrib('class', 'form-control')
            ->setValidators([['NotEmpty', true], 'EmailAddress'])
            ->setDecorators(['ViewHelper', ['HtmlTag', ['tag' => 'div', 'class' => 'form-group']], ['Label', ['class' => 'sr-only']]]);
        $this->addElement($email);

        $code = $this->createElement('text', 'authcode');
        $placeholder = $translator->translate('Access code');
        $code
            ->setLabel('Your access code')
            ->setRequired(true)
            ->setAttrib('placeholder', $placeholder)
            ->setAttrib('class', 'form-control')
            ->setValidators(['NotEmpty'])
            ->setDecorators(['ViewHelper', ['HtmlTag', ['tag' => 'div', 'class' => 'form-group']], ['Label', ['class' => 'sr-only']]]);
        $this->addElement($code);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-default btn-default-alt')
            ->setLabel('Start');
        $this->addElement($submit);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_votingauth', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }
}
