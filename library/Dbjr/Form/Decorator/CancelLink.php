<?php

class Dbjr_Form_Decorator_CancelLink extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $translator = Zend_Registry::get('Zend_Translate');

        $label = $this->getOption('label') ? $this->getOption('label') : $translator->translate('Cancel');
        $prefix = $this->getOption('prefix') ? $this->getOption('prefix') : $translator->translate('or');
        $blockClass = $this->getOption('blockClass') ? $this->getOption('blockClass') : 'cancel-link';

        return $content . '<div class="' . $blockClass . '">' . $prefix . ' <a href="' . $this->getOption('url') . '">' . $label . '</a></div>';
    }
}
