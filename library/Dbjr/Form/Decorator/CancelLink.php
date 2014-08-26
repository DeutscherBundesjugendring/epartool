<?php

class Dbjr_Form_Decorator_CancelLink extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $view = new Zend_View();
        $label = $this->getOption('label') ? $this->getOption('label') : $view->translate('Cancel');
        $prefix = $this->getOption('prefix') ? $this->getOption('prefix') : $view->translate('or');
        $blockClass = $this->getOption('blockClass') ? $this->getOption('blockClass') : 'cancel-link';

        return $content . '<div class="' . $blockClass . '">' . $prefix . ' <a href="' . $this->getOption('url') . '">' . $label . '</a></div>';
    }
}
