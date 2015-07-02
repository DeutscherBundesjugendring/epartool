<?php

class Admin_Form_Mail_Component extends Dbjr_Form_Admin
{

    public function init()
    {
        $this
            ->setMethod('post')
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/mail-component']);

        $name = $this->createElement('text', 'name');
        $name
            ->setLabel('Name')
            ->setRequired(true)
            ->setAttrib('maxlength', 50)
            ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->translate('Max %d characters'), 50))
            ->addValidator('Db_NoRecordExists', false, ['table' => 'email_component', 'field' => 'name'])
            ->addValidator('Regex', false, ['pattern' => '/[_a-z0-9]{3,50}/']);
        $this->addElement($name);

        $bodyText = $this->createElement('textarea', 'body_text');
        $bodyText
            ->setLabel('Message (plain text)')
            ->setRequired(true)
            ->setAttrib('rows', 5);
        $this->addElement($bodyText);

        $bodyHtml = $this->createElement('textarea', 'body_html');
        $bodyHtml
            ->setLabel('Message (HTML)')
            ->setRequired(true)
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_EMAIL)
            ->setAttrib('rows', 5);
        $this->addElement($bodyHtml);

        $desc = $this->createElement('textarea', 'description');
        $desc
            ->setLabel('Component description')
            ->setAttrib('rows', 5);
        $this->addElement($desc);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_mailtemplate', array('salt' => 'unique'));
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
