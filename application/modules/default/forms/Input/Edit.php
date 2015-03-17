<?php

class Default_Form_Input_Edit extends Dbjr_Form_Web
{
    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $this->setMethod('post');

        $thes = $this->createElement('textarea', 'thes');
        $placeholder = sprintf(
            $translator->translate('Here you can type in your contribution (up to %s characters).'),
            300
        );
        $thes
            ->setLabel('Contribution')
            ->setAttrib('cols', 85)
            ->setAttrib('rows', 3)
            ->setRequired(true)
            ->setAttrib('placeholder', $placeholder)
            ->setFilters(['StripTags', 'HtmlEntities'])
            ->addValidators(['NotEmpty']);
        $this->addElement($thes);

        $expl = $this->createElement('textarea', 'expl');
        $placeholder = sprintf($translator->translate('Here you explain your contribution more in depth, e.g. with examples (up to %s characters).'), 2000);
        $expl
            ->setLabel('Explanation')
            ->setAttrib('cols', 85)
            ->setAttrib('rows', 5)
            ->setAttrib('placeholder', $placeholder)
            ->setFilters(['StripTags', 'HtmlEntities']);
        $this->addElement($expl);

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Save');
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
