<?php

/**
 * Class Admin_Form_CreateInput
 */
class Admin_Form_CreateInput extends Admin_Form_Input
{
    public function init()
    {
        $adminUsers = (new Model_Users())->getAdmins();
        $selectOptions = [];
        foreach($adminUsers as $adminUser) {
            $selectOptions[$adminUser->uid] = $adminUser->name;
        }
        $adminUserId = $this->createElement('select', 'uid');
        $adminUserId
            ->setLabel('Contribution Author')
            ->setRequired(true)
            ->setMultiOptions($selectOptions)
            ->addValidator('Int');
        $this->addElement($adminUserId);
        parent::init();
    }
}
