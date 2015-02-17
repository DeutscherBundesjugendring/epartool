<?php

class Admin_Form_Media_Delete extends Dbjr_Form_Admin
{
    public function init()
    {
        $this->setMethod('post');

        $file = $this->createElement('hidden', 'file');
        $this->addElement($file);

        $folder = $this->createElement('hidden', 'folder');
        $this->addElement($folder);

        $kid = $this->createElement('hidden', 'kid');
        $this->addElement($kid);

        $form_num = $this->createElement('hidden', 'form_num');
        $this->addElement($form_num);

        $title = Zend_Registry::get('Zend_Translate')->translate('Delete');
        $submit = $this->createElement('button', 'submit');
        $submit
            ->setActionType(Dbjr_Form_Element_Button::TYPE_DELETE)
            ->setAttrib('title', $title)
            ->setConfirmMessage('Delete media?');
        $this->addElement($submit);

    }

    /**
     * Adds csrf hash element. If there are more then one form on the same page, the elements must have different names
     * @param string $elementName The name of the csrf hash element
     */
    public function addCsrfHash($elementName)
    {
        $hash = $this->createElement('hash', $elementName, array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $hash->setDecorators(array('ViewHelper'));
        $this->addElement($hash);

        return $this;
    }
}
