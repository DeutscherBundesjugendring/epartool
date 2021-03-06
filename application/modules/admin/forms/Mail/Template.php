<?php

class Admin_Form_Mail_Template extends Dbjr_Form_Admin
{
    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $this
            ->setMethod('post')
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/mail-template']);

        $id = $this->createElement('hidden', 'id');
        $this->addElement($id);

        $projectCode = $this->createElement('hidden', 'project_code');
        $this->addElement($projectCode);

        $name = $this->createElement('text', 'name');
        $name
            ->setLabel('Name')
            ->setRequired(true)
            ->setAttrib('maxlength', 50)
            ->setDescription(sprintf($translator->translate('Max %d characters'), 50))
            ->addValidator('Db_NoRecordExists', false, ['table' => 'email_template', 'field' => 'name'])
            ->addValidator('Regex', false, ['pattern' => '/[_a-z0-9]{3,50}/']);
        $this->addElement($name);

        $subject = $this->createElement('text', 'subject');
        $subject
            ->setLabel('Subject')
            ->setRequired(true)
            ->setAttrib('maxlength', 75)
            ->setDescription(sprintf($translator->translate('Max %d characters'), 75));
        $this->addElement($subject);

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

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_mailtemplate', array('salt' => 'unique'));
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
