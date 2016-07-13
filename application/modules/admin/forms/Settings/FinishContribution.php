<?php

class Admin_Form_Settings_FinishContribution extends Dbjr_Form_Admin
{
    public function init()
    {
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'settings/finishContributionForm.phtml'))));

        $this
            ->setMethod('post')
            ->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/settings/finish-contribution')
            ->setAttrib('class', 'offset-bottom')
            ->setAttrib('enctype', 'multipart/form-data');

        $stateLabel = $this->createElement('text', 'state_label');
        $stateLabel
            ->setLabel('State label');
        $this->addElement($stateLabel);

        $fieldSwitchName = $this->createElement('select', 'field_switch_name');
        $fieldSwitchName
            ->setLabel('Display field name')
            ->setRequired(true)
            ->setMultioptions([1 => 'Yes', 0 => 'No']);
        $this->addElement($fieldSwitchName);

        $fieldSwitchAge = $this->createElement('select', 'field_switch_age');
        $fieldSwitchAge
            ->setLabel('Display field age')
            ->setRequired(true)
            ->setMultioptions([1 => 'Yes', 0 => 'No']);
        $this->addElement($fieldSwitchAge);

        $fieldSwitchState = $this->createElement('select', 'field_switch_state');
        $fieldSwitchState
            ->setLabel('Display field state')
            ->setRequired(true)
            ->setMultioptions([1 => 'Yes', 0 => 'No']);
        $this->addElement($fieldSwitchState);

        $fieldSwitchComments = $this->createElement('select', 'field_switch_comments');
        $fieldSwitchComments
            ->setLabel('Display field comments')
            ->setRequired(true)
            ->setMultioptions([1 => 'Yes', 0 => 'No']);
        $this->addElement($fieldSwitchComments);

        $allowGroups = $this->createElement('select', 'allow_groups');
        $allowGroups
            ->setLabel('Allow groups')
            ->setRequired(true)
            ->setMultioptions([1 => 'Yes', 0 => 'No']);
        $this->addElement($allowGroups);

        $fieldSwitchContributionOrigin = $this->createElement('select', 'field_switch_contribution_origin');
        $fieldSwitchContributionOrigin
            ->setLabel('Display field contribution origin')
            ->setRequired(true)
            ->setMultioptions([1 => 'Yes', 0 => 'No']);
        $this->addElement($fieldSwitchContributionOrigin);

        $fieldSwitchIndividualsNum = $this->createElement('select', 'field_switch_individuals_num');
        $fieldSwitchIndividualsNum
            ->setLabel('Display field individuals num')
            ->setRequired(true)
            ->setMultioptions([1 => 'Yes', 0 => 'No']);
        $this->addElement($fieldSwitchIndividualsNum);

        $fieldSwitchGroupName = $this->createElement('select', 'field_switch_group_name');
        $fieldSwitchGroupName
            ->setLabel('Display field group name')
            ->setRequired(true)
            ->setMultioptions([1 => 'Yes', 0 => 'No']);
        $this->addElement($fieldSwitchGroupName);

        $fieldSwitchContactPerson = $this->createElement('select', 'field_switch_contact_person');
        $fieldSwitchContactPerson
            ->setLabel('Display field contact person')
            ->setRequired(true)
            ->setMultioptions([1 => 'Yes', 0 => 'No']);
        $this->addElement($fieldSwitchContactPerson);


        $fieldSwitchNewsletter = $this->createElement('select', 'field_switch_newsletter');
        $fieldSwitchNewsletter
            ->setLabel('Display field newsletter')
            ->setRequired(true)
            ->setMultioptions([1 => 'Yes', 0 => 'No']);
        $this->addElement($fieldSwitchNewsletter);

        $fieldSwitchNotification = $this->createElement('select', 'field_switch_notification');
        $fieldSwitchNotification
            ->setLabel('Display field notification')
            ->setRequired(true)
            ->setMultioptions([1 => 'Yes', 0 => 'No']);
        $this->addElement($fieldSwitchNotification);



        // CSRF Protection
        $hash = $this->createElement('hash', 'csrf_token_finishcontributionadmin', array('salt' => 'unique'));
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
