<?php

class Admin_Form_Settings_Services extends Dbjr_Form_Admin
{

    protected $videoServiceAccess;

    public function init()
    {
        $this->videoServiceAccess = true;
        $this->setAttrib('class', 'offset-bottom');

        // Add Youtube video toggle
        $youtube = $this->createElement('checkbox', 'video_youtube_enabled');
        $youtube
            ->setLabel('YouTube')
            ->setRequired(false);
        $this->addElement($youtube);

        $webserviceConf = (new Zend_Registry ())->get('systemconfig')->webservice;


        // Add Vimeo video toggle
        if ($webserviceConf
            && $webserviceConf->vimeo
            && $webserviceConf->vimeo->accessToken
        ) {
            $vimeo = $this->createElement('checkbox', 'video_vimeo_enabled');
            $vimeo
                ->setLabel('Vimeo')
                ->setRequired(false);
        } else {
            $vimeo = $this->createElement('checkbox', 'video_vimeo_enabled_placeholder');
            $vimeo
                ->setLabel('Vimeo')
                ->setAttrib('disabled', 'disabled');
            $this->videoServiceAccess = false;
        }
        $this->addElement($vimeo);

        // Add Facebook video toggle
        if ($webserviceConf
            && $webserviceConf->facebook
            && $webserviceConf->facebook->appId
            && $webserviceConf->facebook->appSecret
        ) {
            $facebook = $this->createElement('checkbox', 'video_facebook_enabled');
            $facebook
                ->setLabel('Facebook')
                ->setRequired(false);
        } else {
            $facebook = $this->createElement('checkbox', 'video_facebook_enabled_placeholder');
            $facebook
                ->setLabel('Facebook')
                ->setAttrib('disabled', 'disabled');
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
