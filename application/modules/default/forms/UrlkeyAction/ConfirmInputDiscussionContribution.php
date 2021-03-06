<?php

class Default_Form_UrlkeyAction_ConfirmInputDiscussionContribution extends Dbjr_Form_Web
{
    public function init()
    {
        $this->addElement(
            $this
                ->createElement('button', 'confirm')
                ->setAttrib('type', 'submit')
                ->setAttrib('class', 'btn-primary')
                ->setLabel('Confirm')
        );

        $this->addElement(
            $this
                ->createElement('button', 'delete')
                ->setAttrib('type', 'submit')
                ->setAttrib('class', 'btn-danger')
                ->setLabel('Delete')
        );

        $this->addElement(
            $this
                ->createElement('hash', 'csrf_token', array('salt' => 'unique'))
                ->setSalt(md5(mt_rand(1, 100000) . time()))
                ->setTimeout(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl)
        );
    }
}
