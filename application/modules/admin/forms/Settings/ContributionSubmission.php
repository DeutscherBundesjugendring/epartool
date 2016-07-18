<?php

class Admin_Form_Settings_ContributionSubmission extends Dbjr_Form_Admin
{
    public function init()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'settings/contributionSubmissionForm.phtml']]]);

        $stateLabel = $this->createElement('text', 'state_field_label');
        $stateLabel->setLabel('State field label');

        $this->addElement($stateLabel);

        $element = $this->createElement('checkbox', 'field_switch_name');
        $element->setLabel('Display field name');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_age');
        $element->setLabel('Display field age');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_state');
        $element->setLabel('Display field state');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_comments');
        $element->setLabel('Display field comment groups');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'allow_groups');
        $element->setLabel('Allow groups');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_contribution_origin');
        $element->setLabel('Display field contribution origin');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_individuals_sum');
        $element->setLabel('Display field individuals sum');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_group_name');
        $element->setLabel('Display field group name');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_contact_person');
        $element->setLabel('Display field contact person');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_newsletter');
        $element->setLabel('Display field newsletter');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_notification');
        $element->setLabel('Display field notification');
        $this->addElement($element);

        $licenseOptions = [];
        $locale = Zend_Registry::get('Zend_Locale');
        $licenses = (new Model_License())->getLicences($locale->getLanguage() . '_' . $locale->getRegion());
        foreach ($licenses as $license) {
            $licenseOptions[$license['number']] = $license['title'];
        }
        $license = $this->createElement('select', 'license');
        $license
            ->setLabel('License')
            ->setRequired(true)
            ->setMultiOptions($licenseOptions);
        $this->addElement($license);

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