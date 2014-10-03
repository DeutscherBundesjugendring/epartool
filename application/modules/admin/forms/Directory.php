<?php

class Admin_Form_Directory extends Dbjr_Form_Admin
{

    public function init()
    {
        $view = new Zend_View();
        $this->setMethod('post');

        $dirName = $this->createElement('text', 'dir_name');
        $dirName
            ->setLabel('Name')
            ->setRequired(true)
            ->setAttrib('maxlength', 120);
        $this->addElement($dirName);

        $parent = $this->createElement('select', 'parent');
        $parent
            ->setLabel('Parent fodler')
            ->addValidator('Int');
        $this->addElement($parent);

        $position = $this->createElement('select', 'position');
        $position
            ->setLabel('Position')
            ->setRequired(true)
            ->setMultioptions(
                [
                    'PREV_SIBLING'  => $view->translate('Vor diesem Ordner'),
                    'NEXT_SIBLING'  => $view->translate('Nach diesem Ordner'),
                    'FIRST_CHILD' => $view->translate('Als ersten Unterordner'),
                    'LAST_CHILD' => $view->translate('Als letzten Unterordner'),
                ]
            );
        $this->addElement($position);

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Save');
        $this->addElement($submit);
    }
}
