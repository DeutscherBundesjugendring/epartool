<?php

class Admin_Form_Footer extends Dbjr_Form_Admin
{
    private $footers;

    /**
     * @param array $footers An array in format [footerId => footerText]
     */
    public function __construct (array $footers)
    {
        $this->footers = $footers;
        parent::__construct();
    }

    public function init()
    {
        $i = 1;
        $footerCount = count($this->footers);
        $translator = Zend_Registry::get('Zend_Translate');

        foreach ($this->footers as $key => $text) {
            $text = $this->createElement('textarea', $key);
            $text
                ->setLabel(sprintf($translator->translate('Footer %d/%d'), $i, $footerCount))
                ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD)
                ->addValidator('stringLength', ['max' => 100000]);
            $this->addElement($text);
            $i++;
        }

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_partner', array('salt' => 'unique'));
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
