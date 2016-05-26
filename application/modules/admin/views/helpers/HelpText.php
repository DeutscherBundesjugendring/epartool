<?php

class Admin_View_Helper_HelpText extends Zend_View_Helper_Abstract
{
    public function helpText($name)
    {
        $helpText = (new Model_HelpText())->fetchRow(['name = ?' => $name]);
        
        return $this->view->partial('_helpers/helpText.phtml',[
            'helpTextName' => $helpText['name'],
            'helpTextBody' => $helpText['body'],
        ]);
    }
}
