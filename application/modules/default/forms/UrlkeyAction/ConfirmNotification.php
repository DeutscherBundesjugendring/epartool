<?php

class Default_Form_UrlkeyAction_ConfirmNotification extends Zend_Form
{
    public function init()
    {
        $this->addElement(
            $this
                ->createElement('button', 'submit')
                ->setLabel('Confirm')
                ->setAttrib('type', 'submit')
        );

        $this->addElement(
            $this
                ->createElement('hash', 'csrf_token', array('salt' => 'unique'))
                ->setSalt(md5(mt_rand(1, 100000) . time()))
                ->setTimeout(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl)
        );
    }
}
