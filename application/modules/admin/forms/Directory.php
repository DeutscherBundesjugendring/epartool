<?php

class Admin_Form_Directory extends Dbjr_Form_Admin
{

    public function init()
    {
        $this->setMethod('post');

        $dirName = $this->createElement('text', 'dir_name');
        $dirName
            ->setLabel('Name')
            ->setRequired(true)
            ->setAttrib('maxlength', 120);
        $this->addElement($dirName);

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Save');
        $this->addElement($submit);
    }
}
