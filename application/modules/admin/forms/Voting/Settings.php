<?php

class Admin_Form_Voting_Settings extends Dbjr_Form_Admin
{
    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $buttonNum = $this->createElement('number', 'btn_numbers');
        $buttonNum
            ->setLabel('Number of voting buttons')
            ->setAttrib('max', 4)
            ->setAttrib('min', 1)
            ->addValidator('Int')
            ->addValidator('LessThan', false, ['max' => 5])
            ->addValidator('GreaterThan', false, ['min' => 0]);
        $this->addElement($buttonNum);

        $desc = sprintf($translator->translate('Comma separated, lower first, max. %d characters'), 220);
        $buttonLabels = $this->createElement('text', 'btn_labels');
        $buttonLabels
            ->setLabel('Button labels')
            ->setDescription($desc)
            ->setAttrib('maxlength', 220)
            ->setRequired(true);
        $this->addElement($buttonLabels);

        $buttonImportant = $this->createElement('radio', 'is_btn_important');
        $buttonImportant
            ->setLabel('Superbutton')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'y' => $translator->translate('Enable'),
                    'n' => $translator->translate('Disable'),
                ]
            );
        $this->addElement($buttonImportant);

        $buttonNoOpinion = $this->createElement('radio', 'btn_no_opinion');
        $buttonNoOpinion
            ->setLabel('No Opinion')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    1 => $translator->translate('Enable'),
                    0 => $translator->translate('Disable'),
                ]
            );
        $this->addElement($buttonNoOpinion);

        $buttonImportantLabel = $this->createElement('text', 'btn_important_label');
        $buttonImportantLabel->setLabel('Superbutton label');
        $this->addElement($buttonImportantLabel);

        $buttonImportantClicks = $this->createElement('number', 'btn_important_max');
        $buttonImportantClicks
            ->setLabel('Number of clicks allowed')
            ->setAttrib('max', 9999)
            ->setAttrib('min', 1)
            ->addValidator('Int');
        $this->addElement($buttonImportantClicks);

        $buttonImportantFactor = $this->createElement('number', 'btn_important_factor');
        $buttonImportantFactor
            ->setLabel('Rating factor')
            ->setDescription('Max. rating × factor = total points')
            ->setAttrib('max', 8)
            ->setAttrib('min', 1)
            ->addValidator('Int')
            ->addValidator('LessThan', false, ['max' => 8])
            ->addValidator('GreaterThan', false, ['min' => 1]);
        $this->addElement($buttonImportantFactor);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Submit');
        $this->addElement($submit);
    }
}
