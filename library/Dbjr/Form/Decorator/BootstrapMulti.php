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
                ['tag' => 'div', 'class' => 'form-label']
            )
            ->addDecorator('ViewHelper')
            ->addDecorator('Errors')
            ->addDecorator('Description', ['tag' => 'p', 'class' => 'help-block'])
            ->addDecorator(
                ['wrapper' => 'HtmlTag'],
                [
                    'tag' => 'div',
                    'id' => ['callback' => [get_class($element), 'resolveElementId']],
                    'class' => 'form-group',
                ]
            );

        return $element->render();
    }
}
