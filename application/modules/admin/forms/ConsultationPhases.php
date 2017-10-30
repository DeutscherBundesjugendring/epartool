<?php

class Admin_Form_ConsultationPhases extends Dbjr_Form_Admin
{

    public function init()
    {
        $enableNames = $this->createElement('checkbox', 'enableCustomNames');
        $enableNames->setLabel('Enable custom phase names');
        self::addCssClass($enableNames, 'js-enable-custom-consultation-phase-names');
        $this->addElement($enableNames);

        $desc = sprintf(Zend_Registry::get('Zend_Translate')->translate('Max %d characters'), 50);

        $phaseInfo = $this->createElement('text', 'phase_info');
        $phaseInfo
            ->setLabel('Info')
            ->setAttrib('maxlength', 50)
            ->setAttrib('disabled', 'disabled')
            ->setDescription($desc)
            ->addValidator('StringLength', false, ['min' => 3, 'max' => 50]);
        $this->addElement($phaseInfo);

        $phaseSupport = $this->createElement('text', 'phase_support');
        $phaseSupport
            ->setLabel('Questions')
            ->setAttrib('maxlength', 50)
            ->setAttrib('disabled', 'disabled')
            ->setDescription($desc)
            ->addValidator('StringLength', false, ['min' => 3, 'max' => 50]);
        $this->addElement($phaseSupport);

        $phaseInput = $this->createElement('text', 'phase_input');
        $phaseInput
            ->setLabel('Contributions')
            ->setAttrib('maxlength', 50)
            ->setAttrib('disabled', 'disabled')
            ->setDescription($desc)
            ->addValidator('StringLength', false, ['min' => 3, 'max' => 50]);
        $this->addElement($phaseInput);

        $phaseVoting = $this->createElement('text', 'phase_voting');
        $phaseVoting
            ->setLabel('Voting')
            ->setAttrib('maxlength', 50)
            ->setAttrib('disabled', 'disabled')
            ->setDescription($desc)
            ->addValidator('StringLength', false, ['min' => 3, 'max' => 50]);
        $this->addElement($phaseVoting);

        $phaseFollowup = $this->createElement('text', 'phase_followup');
        $phaseFollowup
            ->setLabel('Reaction & Impact')
            ->setAttrib('maxlength', 50)
            ->setAttrib('disabled', 'disabled')
            ->setDescription($desc)
            ->addValidator('StringLength', false, ['min' => 3, 'max' => 50]);
        $this->addElement($phaseFollowup);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Save');
        $this->addElement($submit);
    }

    /**
     * Sets the form input elements as active and checks the relevant checkbox
     */
    public function setActive()
    {
        $this->getElement('enableCustomNames')->setAttrib('checked', 'checked');
        foreach ($this->getElements() as $element) {
            $element->setAttrib('disabled', null);
        }
    }
}
