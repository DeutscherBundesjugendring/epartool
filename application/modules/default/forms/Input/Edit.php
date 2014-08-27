<?php

class Default_Form_Input_Edit extends Zend_Form
{
    public function init()
    {
        $view = new Zend_View();

        $this
            ->setDecorators(array('FormElements', 'Form'))
            ->setElementDecorators(array('ViewHelper', 'Errors', 'Description'))
            ->setAttrib('class', 'form-contribution')
            ->setMethod('post');

        $thes = $this->createElement('textarea', 'thes');
        $placeholder = $view->translate('Hier könnt ihr euren Beitrag mit bis zu 300 Buchstaben schreiben');
        $thes
            ->setLabel('These')
            ->setAttrib('cols', 85)
            ->setAttrib('rows', 2)
            ->setRequired(true)
            ->setAttrib('class', 'input-block-level')
            ->setAttrib('placeholder', $placeholder)
            ->setFilters(['StripTags', 'HtmlEntities'])
            ->addValidators(['NotEmpty']);
        $this->addElement($thes);

        $expl = $this->createElement('textarea', 'expl');
        $placeholder = $view->translate('Hier könnt ihr euren Beitrag mit bis zu 2000 Buchstaben erläutern');
        $expl
            ->setLabel('Erläuterung')
            ->setAttrib('cols', 85)
            ->setAttrib('rows', 5)
            ->setAttrib('class', 'extension input-block-level')
            ->setAttrib('placeholder', $placeholder)
            ->setFilters(['StripTags', 'HtmlEntities']);
        $this->addElement($expl);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setLabel('Save')
            ->setAttrib('class', 'btn pull-left');
        $this->addElement($submit);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_inputedit', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl);
        }
        $this->addElement($hash);
    }
}
