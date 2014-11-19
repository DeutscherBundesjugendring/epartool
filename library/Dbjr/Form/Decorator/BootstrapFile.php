<?php

// The implemented interface is empty, but it has to be implemented as Zend checks for it. Cant figure out why...
class Dbjr_Form_Decorator_BootstrapFile extends Zend_Form_Decorator_Abstract implements Zend_Form_Decorator_Marker_File_Interface
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
            ->addDecorator('Errors', ['class' => 'text-danger-block'])
            ->addDecorator('Description', ['tag' => 'p', 'class' => 'help-block'])
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
