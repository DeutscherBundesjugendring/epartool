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

        $email = $this->createElement('email', 'email');
        $email
            ->setLabel('Email')
            ->setRequired(true)
            ->setAttrib('maxlength', 60)
            ->setDescription(sprintf($translator->translate('Max %d characters'), 60))
            ->addValidator('Db_NoRecordExists', false, ['table' => 'users', 'field' => 'email'])
            ->addValidator('EmailAddress');
        $this->addElement($email);

        $role = $this->createElement('select', 'lvl');
        $role
            ->setLabel('Role')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'usr' => $translator->translate('User'),
                    'edt' => $translator->translate('Editor'),
                    'adm' => $translator->translate('Admin'),
                ]
            )
            ->setValue('usr');
        $this->addElement($role);

        $block = $this->createElement('select', 'block');
        $block
            ->setLabel('Status')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'b' => $translator->translate('Blocked'),
                    'u' => $translator->translate('Unconfirmed'),
                    'c' => $translator->translate('Confirmed'),
                ]
            )
            ->setValue('b');
        $this->addElement($block);

        $note = $this->createElement('textarea', 'cmnt');
        $note
            ->setLabel('Internal note')
            ->setAttrib('rows', 5);
        $this->addElement($note);

        $newsletter = $this->createElement('checkbox', 'newsl_subscr');
        $newsletter
            ->setLabel('Receive newsletter')
            ->setRequired(true)
            ->setOptions(
                [
                    'checkedValue' => 'y',
                    'uncheckedValue' => 'n',
                ]
            )
            ->setValue('n');
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
