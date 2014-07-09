<?php

class Dbjr_Form_Decorator_BootstrapRadio extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();
        $element
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Errors')
            ->addDecorator('Description', ['tag' => 'p', 'class' => 'description'])
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
                    'class' => 'radio',
                ]
            );

        return $element->render();
    }
}
