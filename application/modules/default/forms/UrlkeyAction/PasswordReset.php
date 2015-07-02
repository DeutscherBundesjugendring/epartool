<?php

class Default_Form_UrlkeyAction_PasswordReset extends Dbjr_Form_Web
{
    public function init()
    {
        $minPassLength = Zend_Registry::get('systemconfig')->security->password->minLength;

        $description = '(' . sprintf(Zend_Registry::get('Zend_Translate')->translate('min. %s characters'), $minPassLength) . ')';
        $this->addElement(
            $this
                ->createElement('password', 'password')
                ->setLabel('New password')
                ->setDescription($description)
                ->setRequired(true)
                ->setAttrib('class', 'js-has-password-meter')
                ->addValidator('stringLength', 'min', $minPassLength)
        );


        $identicalValidator = (new Zend_Validate_Identical())
            ->setToken('password')
            ->setStrict(true)
            ->setMessages(
                [
                    Zend_Validate_Identical::NOT_SAME => Zend_Registry::get('Zend_Translate')->translate(
                        'The passwords do not match.'
                    ),
                ]
            );
        $this->addElement(
            $this
                ->createElement('password', 'password_confirm')
                ->setLabel('Confirm new password')
                ->setRequired(true)
                ->addValidator($identicalValidator)
        );

        $this->addElement(
            $this
                ->createElement('button', 'submit')
                ->setAttrib('type', 'submit')
                ->setAttrib('class', 'btn-primary')
                ->setLabel('Save new password')
        );

        $this->addElement(
            $this
                ->createElement('hash', 'csrf_token', array('salt' => 'unique'))
                ->setSalt(md5(mt_rand(1, 100000) . time()))
                ->setTimeout(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl)
        );
    }
}
