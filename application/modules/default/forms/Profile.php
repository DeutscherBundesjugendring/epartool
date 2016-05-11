<?php

class Default_Form_Profile extends Dbjr_Form_Web
{

    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');
        $minPassLength = Zend_Registry::get('systemconfig')->security->password->minLength;

        $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/user/profile');

        $email = $this->createElement('email', 'email');
        $email
            ->setLabel('Email Address')
            ->setAttrib('placeholder', '@')
            ->setValidators(['EmailAddress'])
            ->setAttrib('disabled', 'disabled');
        $this->addElement($email);

        $name = $this->createElement('text', 'name');
        $placeholder = $translator->translate('First name and surname');
        $name
            ->setLabel('Name')
            ->setRequired(false)
            ->setAttrib('placeholder', $placeholder)
            ->setValidators(['NotEmpty'])
            ->setFilters(['StripTags']);
        $this->addElement($name);

        $nick = $this->createElement('text', 'nick');
        $placeholder = $translator->translate('Nick');
        $nick
            ->setLabel('Nick')
            ->setRequired(false)
            ->setAttrib('placeholder', $placeholder)
            ->setFilters(['StripTags']);
        $this->addElement($nick);

        $this->addElement(
            $this
                ->createElement('password', 'password')
                ->setLabel('New password')
                ->setRequired(false)
                ->setAttrib('class', 'js-has-password-meter')
                ->addValidator('stringLength', 'min', $minPassLength)
        );

        $identicalValidator = (new Zend_Validate_Identical())
            ->setToken('password')
            ->setStrict(true)
            ->setMessages(
                [
                    Zend_Validate_Identical::NOT_SAME => Zend_Registry::get('Zend_Translate')->translate(
                        'Passwords do not match.'
                    ),
                ]
            );
        $this->addElement(
            $this
                ->createElement('password', 'password_confirm')
                ->setLabel('Confirm a new password')
                ->setRequired(false)
                ->addValidator($identicalValidator)
        );

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-default')
            ->setLabel('Send');
        $this->addElement($submit);

        $hash = $this->createElement('hash', 'csrf_token_register', ['salt' => 'unique']);
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        $this->addElement($hash);
    }
}
