<?php

class Default_Form_Login extends Dbjr_Form_Web
{
    public function init()
    {
        $this
            ->setMethod('post')
            ->setAttrib('class', 'offset-bottom');

        $email = $this->createElement('email', 'username');
        $email
            ->setLabel('Email Address')
            ->setRequired(true)
            ->setAttrib('placeholder', '@')
            ->setValidators([['NotEmpty', true], 'EmailAddress']);
        $this->addElement($email);

        $pass = $this->createElement('password', 'password');
        $pass
            ->setLabel('Password')
            ->setRequired(true)
            ->setValidators(['NotEmpty']);
        $this->addElement($pass);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setLabel('Login')
            ->setAttrib('class', 'btn-default btn-sm');
        $this->addElement($submit);


        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_login', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }
}
