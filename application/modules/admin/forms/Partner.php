<?php

class Admin_Form_Partner extends Dbjr_Form_Admin
{
    public function init()
    {
        $id = $this->createElement('hidden', 'id');
        $this->addElement($id);

        $nameEl = $this->createElement('text', 'name');
        $nameEl
            ->setLabel('Name')
            ->addValidator('stringLength', ['max' => 255]);
        $this->addElement($nameEl);

        $descEl = $this->createElement('text', 'description');
        $descEl
            ->setLabel('Description')
            ->addValidator('stringLength', ['max' => 255]);
        $this->addElement($descEl);

        $urlEl = $this->createElement('text', 'link_url');
        $urlEl
            ->setLabel('Link URL')
            ->addValidator('stringLength', ['max' => 255]);
        $this->addElement($urlEl);

        $urlEl = $this->createElement('text', 'link_url');
        $urlEl
            ->setLabel('Link URL')
            ->addValidator('stringLength', ['max' => 255]);
        $this->addElement($urlEl);

        $imageEl = $this->createElement('media', 'image');
        $imageEl
            ->setLabel('Logo')
            ->addValidator('stringLength', ['max' => 255]);
        $this->addElement($imageEl);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_partner', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary')
            ->setLabel('Save');
        $this->addElement($submit);
    }
}
