<?php

class Admin_Form_Voting_Settings extends Dbjr_Form_Admin
{
    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $buttonType = $this->createElement('radio', 'button_type');
        $buttonType
            ->setRequired(true)
            ->setMultiOptions(
                [
                    Service_Voting::BUTTONS_TYPE_STARS => $translator->translate('Stars'),
                    Service_Voting::BUTTONS_TYPE_HEARTS => $translator->translate('Hearts'),
                    Service_Voting::BUTTONS_TYPE_YESNO => $translator->translate('Yes/No'),
                ]
            );
        $this->addElement($buttonType);

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

        $buttonImportant = $this->createElement('radio', 'is_btn_important');
        $buttonImportant
            ->setLabel('Superbutton')
            ->setRequired(true)
            ->setMultiOptions(
                [
                    '1' => $translator->translate('Enable'),
                    '0' => $translator->translate('Disable'),
                ]
            );
        $this->addElement($buttonImportant);

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

        $submit = $this->createElement('button', 'preview');
        $submit
            ->setAttrib('class', 'btn-raised btn-default')
            ->setAttrib('data-toggle', 'modal')
            ->setAttrib('data-target', '#votingButtonsPreviewModal')
            ->setLabel('Preview');
        $this->addElement($submit);
    }
}
