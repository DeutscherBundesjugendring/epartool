<?php

class Admin_Form_Voting_Settings extends Dbjr_Form_Admin
{
    public function init()
    {
        $view = new Zend_View();

        $this->setMethod('post');

        $buttonNum = $this->createElement('number', 'btn_numbers');
        $buttonNum
            ->setLabel('Number of Voting Buttons')
            ->setAttrib('max', 4)
            ->setAttrib('min', 1)
            ->addValidator('Int')
            ->addValidator('LessThan', false, ['max' => 5])
            ->addValidator('GreaterThan', false, ['min' => 0]);
        $this->addElement($buttonNum);

        $desc = sprintf($view->translate('Comma separated, lower first, max. %d characters'), 220);
        $buttonLabels = $this->createElement('text', 'btn_labels');
        $buttonLabels
            ->setLabel('Button labels')
            ->setDescription($desc)
            ->setAttrib('maxlength', 220)
            ->setRequired(true);
        $this->addElement($buttonLabels);

        $buttonImportant = $this->createElement('radio', 'btn_important');
        $buttonImportant
            ->setLabel('Superbutton')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    'y' => $view->translate('Enable'),
                    'n' => $view->translate('Disable'),
                ]
            );
        $this->addElement($buttonImportant);

        $buttonImportantLabel = $this->createElement('text', 'btn_important_label');
        $buttonImportantLabel->setLabel('Superbutton Label');
        $this->addElement($buttonImportantLabel);

        $buttonImportantClicks = $this->createElement('number', 'btn_important_max');
        $buttonImportantClicks
            ->setLabel('Number of Clicks Allowed')
            ->setAttrib('max', 3)
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
        $submit->setLabel('Submit');
        $this->addElement($submit);
    }
}
