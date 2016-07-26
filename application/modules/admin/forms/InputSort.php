<?php

class Admin_Form_InputSort extends Dbjr_Form_Admin
{

    public function init()
    {
        $translator = Zend_Registry::get('Zend_Translate');
        $this->setAttrib('class', 'offset-bottom');

        $selectOptions = [
            'tid ASC' => $translator->translate('Sort by date'),
            'spprts DESC' => $translator->translate('Sort by number of supporters'),
        ];
        $sortColumn = $this->createElement('select', 'sortColumn');
        $sortColumn
            ->setRequired(true)
            ->setMultiOptions($selectOptions);
        $this->addElement($sortColumn);

        $submit = $this->createElement('submit', 'submitSort');
        $submit->setAttrib('class', 'btn btn-default btn-block');
        $submit->setLabel($translator->translate('Sort'));
        $this->addElement($submit);
    }
}
