<?php

class Admin_Form_Settings_Voting extends Dbjr_Form_Admin
{
    public function init()
    {
        $votingQuestion = $this->createElement('text', 'voting_question');
        $votingQuestion
            ->setLabel('Voting Question')
            ->setRequired(true);
        $this->addElement($votingQuestion);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setAttrib('class', 'btn-primary btn-raised')
            ->setLabel('Save');
        $this->addElement($submit);
    }
}
