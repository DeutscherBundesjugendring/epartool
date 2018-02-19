<?php

class Admin_Form_Settings_ContributionSubmission extends Dbjr_Form_Admin
{
    public function init()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'settings/contributionSubmissionForm.phtml']]]);

        $licenseOptions = [];
        $locale = Zend_Registry::get('Zend_Locale');
        $licenses = (new Model_License())->getLicences($locale->getLanguage() . '_' . $locale->getRegion());
        foreach ($licenses as $license) {
            $licenseOptions[$license['number']] = $license['title'];
        }
        $license = $this->createElement('select', 'license');
        $license
            ->setLabel('License/Information')
            ->setRequired(true)
            ->setMultiOptions($licenseOptions);
        $this->addElement($license);

        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_settingscontributionsubmissionformadmin', array('salt' => 'unique'));
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
