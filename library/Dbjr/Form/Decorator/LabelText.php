<?php

class Dbjr_Form_Decorator_LabelText extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $isEscape = $this->getOption('escape');
        $view = new Zend_View();
        return $content . ($isEscape ? $view->escape($this->getElement()->getLabel()) : $this->getElement()->getLabel());
    }
}
