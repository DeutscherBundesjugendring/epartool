<?php

class Admin_Form_User_Edit extends Dbjr_Form_Admin
{
    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $userId = $this->createElement('hidden', 'uid');
        $this->addElement($userId);

        $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/user/edit')
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/user']);

        $name = $this->createElement('text', 'name');
        $name
            ->setLabel('Name')
            ->setAttrib('maxlength', 80)
            ->setDescription(sprintf($translator->translate('Max %d characters'), 80));
        $this->addElement($name);

        $email = $this->createElement('email', 'email');
        $email
            ->setLabel('Email')
            ->setRequired(true)
            ->setAttrib('maxlength', 60)
            ->setDescription(sprintf($translator->translate('Max %d characters'), 60))
            ->addValidator(
                'Db_NoRecordExists',
                false,
                [
                    'table' => 'users',
                    'field' => 'email',
                ]
            )
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

        $pass = $this->createElement('password', 'password');
        $pass
            ->setLabel('Password')
            ->setAttrib('maxlength', 60);
        $this->addElement($pass);

        $pass = $this->createElement('password', 'password');
        $pass
            ->setLabel('Password')
            ->setAttrib('maxlength', 60)
            ->setDescription('Leave blank to leave unchanged.')
            ->addValidator(
                'stringLength',
                'min',
                Zend_Registry::get('systemconfig')->security->password->minLength
            );
        $this->addElement($pass);

        $passConfirm = $this->createElement('password', 'password_confirm');
        $passConfirm
            ->setLabel('Password confirmation')
            ->setAttrib('maxlength', 60)
            ->addValidator('identical', true, 'password');
        $this->addElement($passConfirm);

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
                    'checkedValue' => 'y',
                    'uncheckedValue' => 'n',
                ]
            )
            ->setValue('n');
        $this->addElement($newsletter);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_useredit', array('salt' => 'unique'));
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

    public function isValid($data)
    {
        $this
            ->getElement('email')
            ->getValidator('Db_NoRecordExists')
            ->setExclude(['field' => 'uid', 'value' => $data['uid']]);

        if ($data['password']) {
            $this->getElement('password_confirm')->setRequired(true);
        } else {
            $this->getElement('password_confirm')->setRequired(false);
        }

        return parent::isValid($data);
    }
}
