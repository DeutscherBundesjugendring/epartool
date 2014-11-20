<?php

class Admin_Form_Followup_Snippet extends Dbjr_Form_Admin
{
    protected $_cancelUrl;

    public function __construct($cancelUrl = null)
    {
        $this->_cancelUrl = $cancelUrl;
        parent::__construct();
    }

    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $this->setMethod('post')
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => $this->_cancelUrl]);

        $expl = $this->createElement('textarea', 'expl');
        $expl
            ->setLabel('Explanation')
            ->setAttrib('rows', 5)
            ->setAttrib('rows', 10000)
            ->addFilter('HtmlEntities')
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD);
        $this->addElement($expl);

        $type = $this->createElement('radio', 'typ');
        $type
            ->setLabel('Type')
            ->setRequired(true)
            ->setMultioptions(Model_Followups::getTypes())
            ->setValue('g');
        $this->addElement($type);

        $hierarchy = $this->createElement('radio', 'hlvl');
        $hierarchy
            ->setLabel('Hierarchy')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    '0' => $translator->translate('Footnote'),
                    '1' => $translator->translate('Body'),
                    '2' => $translator->translate('Heading 1'),
                    '3' => $translator->translate('Heading 2'),
                    '4' => $translator->translate('Heading 3'),
                    '5' => $translator->translate('Heading 4'),
                    '6' => $translator->translate('Heading 5'),
                ]
            )
            ->setValue(1);
        $this->addElement($hierarchy);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_snippet', array('salt' => 'unique'));
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
