<?php

class Admin_Form_Settings_Services extends Dbjr_Form_Admin
{

    public function init()
    {
        $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/settings/services')
            ->setAttrib('class', 'offset-bottom');

        $youtube = $this->createElement('checkbox', 'video_youtube_enabled');
        $youtube
            ->setLabel('Youtube')
            ->setRequired(false);
        $this->addElement($youtube);

        $vimeo = $this->createElement('checkbox', 'video_vimeo_enabled');
        $vimeo
            ->setLabel('Vimeo')
            ->setRequired(false);
        $this->addElement($vimeo);

        $facebook = $this->createElement('checkbox', 'video_facebook_enabled');
        $facebook
            ->setLabel('Facebook')
            ->setRequired(false);
        $this->addElement($facebook);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_tagadmin', array('salt' => 'unique'));
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
