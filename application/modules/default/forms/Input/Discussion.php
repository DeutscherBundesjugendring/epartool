<?php

class Default_Form_Input_Discussion extends Zend_Form
{

    public function init()
    {
        $view = new Zend_View();

        $this->setMethod('post');


        $body = $this->createElement('textarea', 'body');
        $placeholder = $view->translate('Your discussion contribution');
        $body
            ->setRequired(true)
            ->setAttrib('placeholder', $placeholder);
        $this->addElement($body);


        $email = $this->createElement('text', 'email');
        $email
            ->setLabel('Email Address')
            ->setRequired(true)
            ->setAttrib('placeholder', '@')
            ->setValidators([['NotEmpty', true], 'EmailAddress']);
        $this->addElement($email);


        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setLabel('Send')
            ->setAttrib('class', 'btn');
        $this->addElement($submit);

        $hash = $this->createElement('hash', 'csrf_token_register', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }
}
