<?php

/**
 * Class Admin_Form_CreateInput
 */
class Admin_Form_CreateInput extends Admin_Form_Input
{
    public function init()
    {
        $users = (new Model_Users())->getAllConfirmed();
        $selectOptions = [];
        foreach ($users as $user) {
            $selectOptions[$user['uid']] = $user['name'];
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
