<?php

class Dbjr_Form_Decorator_BootstrapFile extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();
        $element
            ->clearDecorators()
            ->addDecorator(
                'Label',
                ['escape' => $this->getOption('escapeLabel') === false ? false : true]
            )
            ->addDecorator('File')
            ->addDecorator('Errors')
            ->addDecorator('Description', ['tag' => 'p', 'class' => 'description'])
            ->addDecorator(
                'HtmlTag',
                [
                    'tag' => 'div',
                    'id' => ['callback' => [get_class($element), 'resolveElementId']],
                    'class' => 'form-group',
                ]
            );

        return $element->render();
    }
}
