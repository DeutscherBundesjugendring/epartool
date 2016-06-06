<?php

class Admin_Form_Tag extends Dbjr_Form_Admin
{

    public function init()
    {

        $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/tag/create')
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/tag']);

        $tag = $this->createElement('text', 'tg_de');
        $tag
            ->setLabel('New keyword')
            ->setRequired(true);
        $this->addElement($tag);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_tagadmin', array('salt' => 'unique'));
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
