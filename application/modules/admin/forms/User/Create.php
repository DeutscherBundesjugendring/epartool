<?php

class Admin_Form_User_Create extends Dbjr_Form_Admin
{
    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $this
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/user']);

        $name = $this->createElement('text', 'name');
        $name
            ->setLabel('Name')
            ->setRequired(true)
            ->setAttrib('maxlength', 80)
            ->setDescription(sprintf($translator->translate('Max %d characters'), 80));
        $this->addElement($name);

        $nick = $this->createElement('text', 'nick');
        $nick
            ->setLabel('Nick')
            ->setAttrib('maxlength', 255)
            ->setDescription(sprintf($translator->translate('Max %d characters'), 255));
        $this->addElement($nick);

        $email = $this->createElement('email', 'email');
        $email
            ->setLabel('Email')
            ->setRequired(true)
            ->setAttrib('maxlength', 60)
            ->setDescription(sprintf($translator->translate('Max %d characters'), 60))
            ->addValidator('Db_NoRecordExists', false, ['table' => 'users', 'field' => 'email'])
            ->addValidator('EmailAddress');
        $this->addElement($email);

        $role = $this->createElement('select', 'role');
        $role
            ->setLabel('Role')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    Model_Users::ROLE_USER => $translator->translate('User'),
                    Model_Users::ROLE_EDITOR => $translator->translate('Editor'),
                    Model_Users::ROLE_ADMIN => $translator->translate('Admin'),
                ]
            )
            ->setValue(Model_Users::ROLE_USER);
        $this->addElement($role);

        $block = $this->createElement('select', 'is_confirmed');
        $block
            ->setLabel('Status')
            ->setMultiOptions(
                [
                    '' => $translator->translate('Unconfirmed'),
                    '0' => $translator->translate('Blocked'),
                    '1' => $translator->translate('Confirmed'),
                ]
            )
            ->setValue('0');
        $this->addElement($block);

        $note = $this->createElement('textarea', 'cmnt');
        $note
            ->setLabel('Internal note')
            ->setAttrib('rows', 5);
        $this->addElement($note);

        $newsletter = $this->createElement('checkbox', 'is_subscribed_newsletter');
        $newsletter
            ->setLabel('Receive newsletter')
            ->setRequired(true)
            ->setOptions(
                [
                    'checkedValue' => '1',
                    'uncheckedValue' => '0',
                ]
            )
            ->setValue('0');
        $this->addElement($newsletter);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_usercreate', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Save');
        $this->addElement($submit);
    }
}
