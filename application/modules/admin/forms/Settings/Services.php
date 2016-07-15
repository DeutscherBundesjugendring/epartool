<?php

class Admin_Form_Settings_Services extends Dbjr_Form_Admin
{

    protected $videoServiceAccess;

    public function init()
    {
        $this->videoServiceAccess = true;
        $this->setAttrib('class', 'offset-bottom');

        $youtube = $this->createElement('checkbox', 'video_youtube_enabled');
        $youtube
            ->setLabel('YouTube')
            ->setRequired(false);
        $this->addElement($youtube);

        $systemConfig = (new Zend_Registry ())->get('systemconfig');

        $vimeo = $this->createElement('checkbox', 'video_vimeo_enabled');
        $vimeo
            ->setLabel('Vimeo')
            ->setRequired(false);
        if (!isset($systemConfig->webservice) || !isset($systemConfig->webservice->vimeo)
            || !isset($systemConfig->webservice->vimeo->accessToken)) {
            $vimeo->setAttrib('disabled', 'disabled');
            $this->videoServiceAccess = false;
        }
        $this->addElement($vimeo);

        $facebook = $this->createElement('checkbox', 'video_facebook_enabled');
        $facebook
            ->setLabel('Facebook')
            ->setRequired(false);
        if (!isset($systemConfig->webservice) || !isset($systemConfig->webservice->facebook)
            || !isset($systemConfig->webservice->facebook->appId)
            || !isset($systemConfig->webservice->facebook->appSecret)) {
            $facebook->setAttrib('disabled', 'disabled');
            $this->videoServiceAccess = false;
        }
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
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Save');
        $this->addElement($submit);
    }

    public function getVideosServiceAccess()
    {
        return $this->videoServiceAccess;
    }
}
