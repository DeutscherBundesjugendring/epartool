<?php

class Admin_Form_AnonymousContributionSubmission extends Dbjr_Form_Admin
{
    /**
     * @var int
     */
    private $consultationId;

    /**
     * @param array|null $options
     * @param int|null $consultationId
     */
    public function __construct($options = null, $consultationId = null)
    {
        $this->consultationId = $consultationId;
        parent::__construct($options);
    }

    public function init()
    {
        $this->setDecorators([['ViewScript', [
            'viewScript' => 'consultation/anonymousContributionSubmissionForm.phtml',
        ]]]);

        $element = $this->createElement('textarea', 'anonymous_contribution_finish_info');
        $element
            ->setLabel('Anonymous Contribution finish info')
            ->setRequired(true)
            ->setAttrib('rows', 5)
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD, $this->consultationId);
        $this->addElement($element);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_anonymouscontributionsubmissionformadmin', array('salt' => 'unique'));
        $hash->setSalt(md5(mt_rand(1, 100000) . time()));
        if (is_numeric((Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl))) {
            $hash->setTimeout(Zend_Registry::get('systemconfig')->adminform->general->csfr_protect->ttl);
        }
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submitAnonymous');
        $submit
            ->setAttrib('class', 'btn-primary')
            ->setLabel('Save');
        $this->addElement($submit);
    }
}
