<?php

class Dbjr_Form_Decorator_BootstrapCheckbox extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();
        $element
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Errors', ['class' => 'text-danger-block'])
            ->addDecorator('Description', ['tag' => 'p', 'class' => 'help-block'])
            ->addDecorator(
                'labelText',
                ['escape' => $this->getOption('escapeLabel') === false ? false : true]
            )
            ->addDecorator(
                ['label' => 'HtmlTag'],
                ['tag' => 'label']
            )
            ->addDecorator(
                ['wrapper' => 'HtmlTag'],
                [
                    'tag' => 'div',
                    'id' => ['callback' => [get_class($element), 'resolveElementId']],
                    'class' => 'checkbox',
                ]
            );

        return $element->render();
    }
}
