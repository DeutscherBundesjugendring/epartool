<?php

class Admin_Form_User_Edit extends Dbjr_Form_Admin
{
    public function init()
    {
        $view = new Zend_View();

        $userId = $this->createElement('hidden', 'uid');
        $this->addElement($userId);

        $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/user/edit');

        $name = $this->createElement('text', 'name');
        $name
            ->setLabel('Name')
            ->setAttrib('maxlength', 80)
            ->setDescription(sprintf($view->translate('Max %d characters.'), 80));
        $this->addElement($name);

        $email = $this->createElement('email', 'email');
        $email
            ->setLabel('Email')
            ->setRequired(true)
            ->setAttrib('maxlength', 60)
            ->setDescription(sprintf($view->translate('Max %d characters.'), 60))
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

        $role = $this->createElement('radio', 'role');
        $role
            ->setLabel('Role')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'usr' => $view->translate('User'),
                    'edt' => $view->translate('Editor'),
                    'adm' => $view->translate('Admin'),
                ]
            )
            ->setValue('usr');
        $this->addElement($role);

        $newsletter = $this->createElement('radio', 'newsl_subscr');
        $newsletter
            ->setLabel('Newsletter subscription')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'y' => $view->translate('Yes'),
                    'n' => $view->translate('No'),
                ]
            )
            ->setValue('n');
        $this->addElement($newsletter);

        $block = $this->createElement('radio', 'block');
        $block
            ->setLabel('Block')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'b' => $view->translate('Blocked'),
                    'u' => $view->translate('Unconfirmed'),
                    'c' => $view->translate('Confirmed'),
                ]
            )
            ->setValue('b');
        $this->addElement($block);

        $note = $this->createElement('textarea', 'cmnt');
        $note
            ->setLabel('Internal note')
            ->setAttrib('rows', 5);
        $this->addElement($note);

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


        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_useredit', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Save');
        $this->addElement($submit);
    }

    public function isValid($data)
    {
        $this
            ->getElement('email')
            ->getValidator('Db_NoRecordExists')
            ->setExclude(['field' => 'uid', 'value' => $data['uid']]);

        return parent::isValid($data);
    }
}
