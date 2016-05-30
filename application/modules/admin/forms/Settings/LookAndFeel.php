<?php

class Admin_Form_Settings_LookAndFeel extends Dbjr_Form_Admin
{
    public function init()
    {
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'settings/lookAndFeelForm.phtml'))));
        
        $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/settings/look-and-feel')
            ->setAttrib('class', 'offset-bottom')
            ->setAttrib('enctype', 'multipart/form-data');

        $logo = $this->createElement('media', 'logo');
        $logo
            ->setLabel('Logo');
        $this->addElement($logo);
        
        $favIcon = $this->createElement('media', 'favicon');
        $favIcon
            ->setLabel('Favicon');
        $this->addElement($favIcon);
        
        $theme = $this->createElement('radioTheme', 'theme_id');
        $theme
            ->setRequired(false);
        $this->addElement($theme);
        
        $colorHeadings = $this->createElement('text', 'color_headings');
        $colorHeadings
            ->setLabel('Headings')
            ->setRequired(true)
            ->addValidator('stringLength', false, [3,6]);
        $this->addElement($colorHeadings);
        
        $colorFrameBackground = $this->createElement('text', 'color_frame_background');
        $colorFrameBackground
            ->setLabel('Frame Background')
            ->setRequired(true)
            ->addValidator('stringLength', false, [3,6]);
        $this->addElement($colorFrameBackground);
        
        $colorLinkActive = $this->createElement('text', 'color_active_link');
        $colorLinkActive
            ->setLabel('Link Active')
            ->setRequired(true)
            ->addValidator('stringLength', false, [3,6]);
        $this->addElement($colorLinkActive);
        
        $mitmachenBubble = $this->createElement('checkbox', 'mitmachen_bubble');
        $mitmachenBubble
            ->setLabel('Add MitMachen bubble')
            ->setRequired(false);
        $this->addElement($mitmachenBubble);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_lookandfeeladmin', array('salt' => 'unique'));
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
