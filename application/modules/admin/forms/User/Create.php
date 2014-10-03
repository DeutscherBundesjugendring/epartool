<?php

class Admin_Form_User_Create extends Dbjr_Form_Admin
{
    public function init()
    {
        $view = new Zend_View();

        $this
            ->setmethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/user/create');

        $name = $this->createElement('text', 'name');
        $name
            ->setLabel('Name')
            ->setRequired(true)
            ->setAttrib('maxlength', 80)
            ->setDescription(sprintf($view->translate('Max %d characters.'), 80));
        $this->addElement($name);

        $email = $this->createElement('email', 'email');
        $email
            ->setLabel('Email')
            ->setRequired(true)
            ->setAttrib('maxlength', 60)
            ->setDescription(sprintf($view->translate('Max %d characters.'), 60))
            ->addValidator('Db_NoRecordExists', false, ['table' => 'users', 'field' => 'email'])
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

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_usercreate', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Save');
        $this->addElement($submit);
    }
}
