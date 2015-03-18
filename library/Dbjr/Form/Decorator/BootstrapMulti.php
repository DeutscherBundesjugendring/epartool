<?php

class Dbjr_Form_Decorator_BootstrapMulti extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();
        $element
            ->clearDecorators()
            ->addDecorator(
                'labelText',
                ['escape' => $this->getOption('escapeLabel') === false ? false : true]
            )
            ->addDecorator(
                ['div-label' => 'HtmlTag'],
                ['tag' => 'h3']
            )
            ->addDecorator('ViewHelper')
            ->addDecorator('Errors', ['class' => 'text-danger-block'])
            ->addDecorator(
                'Description',
                [
                    'tag' => 'p',
                    'class' => 'help-block',
                    'escape' => $this->getOption('escapeDescription') === false ? false : true
                ]
            )
            ->addDecorator(
                ['wrapper' => 'HtmlTag'],
                [
                    'tag' => 'div',
                    'id' => ['callback' => [get_class($element), 'resolveElementId']],
                    'class' => 'checkbox radio',
                ]
            );

        return $element->render();
    }
}
