<?php

class Admin_Form_Followup_Snippet extends Dbjr_Form_Admin
{
    protected $_cancelUrl;
    
    private $consultationId;

    public function __construct($cancelUrl = null, $consultationId = null)
    {
        $this->_cancelUrl = $cancelUrl;
        $this->consultationId = $consultationId;
        parent::__construct();
    }

    public function init()
    {
        $this
            ->setAttrib('class', 'offset-bottom')
            ->setCancelLink(['url' => $this->_cancelUrl]);

        $expl = $this->createElement('textarea', 'expl');
        $expl
            ->setLabel('Explanation')
            ->setAttrib('rows', 5)
            ->setAttrib('rows', 10000)
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD, $this->consultationId);
        $this->addElement($expl);

        $type = $this->createElement('radio', 'type');
        $type
            ->setLabel('Type')
            ->setRequired(true)
            ->setMultioptions(Model_Followups::getTypes())
            ->setValue(Model_Followups::TYPE_GENERAL);
        $this->addElement($type);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_snippet', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);

        $submitAndEdit = $this->createElement('submit', 'submitAndEdit');
        $submitAndEdit
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Save and continue editing');
        $this->addElement($submitAndEdit);

        $submitAndIndex = $this->createElement('submit', 'submitAndIndex');
        $submitAndIndex
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Save and return to index');
        $this->addElement($submitAndIndex);
    }
}
