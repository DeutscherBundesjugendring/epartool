<?php

class Admin_Form_Settings_LookAndFeel extends Dbjr_Form_Admin
{
    public function init()
    {
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'settings/lookAndFeelForm.phtml'))));

        $this
            ->setAttrib('class', 'offset-bottom')
            ->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $logo = $this->createElement('media', 'logo');
        $this->addElement($logo);

        $favIcon = $this->createElement('media', 'favicon');
        $this->addElement($favIcon);

        $theme = $this->createElement('radioTheme', 'theme_id');
        $theme
            ->setRequired(false);
        $this->addElement($theme);

        $colorFrameBackground = $this->createElement('text', 'color_primary');
        $colorFrameBackground
            ->setLabel('Primary color')
            ->setRequired(true)
            ->addValidator('stringLength', false, 7);
        $this->addElement($colorFrameBackground);

        $colorHeadings = $this->createElement('text', 'color_accent_1');
        $colorHeadings
            ->setLabel('Accent color 1')
            ->setRequired(true)
            ->addValidator('stringLength', false, 7);
        $this->addElement($colorHeadings);

        $colorLinkActive = $this->createElement('text', 'color_accent_2');
        $colorLinkActive
            ->setLabel('Accent color 2')
            ->setRequired(true)
            ->addValidator('stringLength', false, 7);
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
