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
            ->setAttrib('disabled', 'disabled');
        $this->addElement($email);

        $name = $this->createElement('text', 'name');
        $placeholder = $translator->translate('First name and surname');

        $name
            ->setLabel('Name')
            ->setRequired(false)
            ->setAttrib('placeholder', $placeholder)
            ->setValidators(['NotEmpty'])
            ->addValidator(new Zend_Validate_StringLength(['max'=>80]))
            ->setFilters(['StripTags']);
        $this->addElement($name);

        $nick = $this->createElement('text', 'nick');
        $placeholder = $translator->translate('Nick');

        $nick
            ->setLabel('Nick')
            ->setRequired(false)
            ->setAttrib('placeholder', $placeholder)
            ->setFilters(['StripTags'])
            ->setDescription('Will be shown instead of your name, e.g in discussions')
            ->addValidator(new Zend_Validate_StringLength(['max'=>255]));
        $this->addElement($nick);

        $description = $translator->translate('If you don\'t want to change your password, leave these fields blank.');
        $this->addElement(
            $this
                ->createElement('password', 'current_password')
                ->setLabel('Current password')
                ->setRequired(false)
            ->setDescription($description)
        );
        
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

    /**
     * Validate the form
     *
     * @param  array $data
     * @return bool
     */
    public function isValid($data)
    {
        if (!empty($data['password']) || !empty($data['password_confirm']) || !empty($data['current_password'])) {
            $this->getElement('password')->setRequired(true);
            $this->getElement('password_confirm')->setRequired(true);
            $this->getElement('current_password')->setRequired(true);
        }
        return parent::isValid($data);
    }
}
