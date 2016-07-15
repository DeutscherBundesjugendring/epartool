<?php

class Admin_Form_Settings_ContributionSubmission extends Dbjr_Form_Admin
{
    public function init()
    {
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'settings/contributionSubmissionForm.phtml'))));

        $stateLabel = $this->createElement('text', 'state_label');
        $stateLabel->setLabel('State label');
        $this->addElement($stateLabel);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_contributionsubmissionformadmin', array('salt' => 'unique'));
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
