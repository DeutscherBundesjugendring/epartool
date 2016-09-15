<?php

class Admin_Form_ContributionSubmission extends Dbjr_Form_Admin
{
    public function init()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'consultation/contributionSubmissionForm.phtml']]]);

        $stateLabel = $this->createElement('text', 'state_field_label');
        $stateLabel->setLabel('Label for self-defined field');
        $this->addElement($stateLabel);

        $element = $this->createElement('textarea', 'contribution_confirmation_info');
        $element
            ->setLabel('Contribution confirmation info')
            ->setRequired(true)
            ->setAttrib('rows', 5)
            ->setWysiwygType(Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD);
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_name');
        $element->setLabel('Display field "Name"');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_age');
        $element->setLabel('Display field "Age"');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_state');
        $element->setLabel('Display self-defined field (e.g. "State")');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_comments');
        $element->setLabel('Display field "Comment Groups"');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'allow_groups');
        $element->setLabel('Allow groups');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_contribution_origin');
        $element->setLabel('Display field "Contribution Origin"');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_individuals_sum');
        $element->setLabel('Display field "Group Size"');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_group_name');
        $element->setLabel('Display field "Group Name"');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_contact_person');
        $element->setLabel('Display field "Contact Person"');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_newsletter');
        $element->setLabel('Display field "Subscribe to Newsletter"');
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'field_switch_notification');
        $element->setLabel('Display field "Notification about Results"');
        $this->addElement($element);

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
