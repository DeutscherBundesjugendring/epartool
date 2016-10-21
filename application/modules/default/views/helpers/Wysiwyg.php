<?php

class Module_Default_View_Helper_Wysiwyg extends Zend_View_Helper_Abstract
{
    public function wysiwyg($string)
    {
        return (new Service_Wysiwyg($this->view->baseUrl()))->placeholderToBasePath($string);
    }
}
