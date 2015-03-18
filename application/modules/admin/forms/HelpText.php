<?php

class Admin_Form_HelpText extends Dbjr_Form_Admin
{

    public function init()
    {

        $this
            ->setMethod('post')
            ->setCancelLink(
                ['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/settings/help-text-index']
            );

        $body = $this->createElement('textarea', 'body');
        $body
            ->setLabel('Body')
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD)
            ->addValidator('StringLength', false, ['max' => 100000])
            ->setRequired(true);
        $this->addElement($body);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_tagadmin', array('salt' => 'unique'));
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
