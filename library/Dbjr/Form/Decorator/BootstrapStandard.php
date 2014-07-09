<?php

class Dbjr_Form_Decorator_BootstrapStandard extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();
        $element
            ->setAttrib('class', 'form-control')
            ->clearDecorators()
            ->addDecorator(
                'Label',
                ['escape' => $this->getOption('escapeLabel') === false ? false : true]
            )
            ->addDecorator('ViewHelper')
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
