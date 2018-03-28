<?php

class Admin_View_Helper_EmailPlaceholderDescription extends Zend_View_Helper_Abstract
{
    /**
     * @param string $name
     * @throws \Exception
     * @return string
     */
    public function EmailPlaceholderDescription($name)
    {
        return Model_Mail_Placeholder::getPlaceHolderInfo($name);
    }
}
