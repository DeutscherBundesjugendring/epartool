<?php

class Dbjr_Form_Decorator_CancelLink extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $view = new Zend_View();
        return $content . $view->translate('or') . ' <a href="' . $this->getOption('url') . '">' . $view->translate('Cancel') . '</a>';
    }
}
