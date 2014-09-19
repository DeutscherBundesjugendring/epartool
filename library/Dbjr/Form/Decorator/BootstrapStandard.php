<?php

class Dbjr_Form_Decorator_BootstrapStandard extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();

        if ($element instanceof Dbjr_Form_Element_Textarea
            && $element->getWysiwygType() === Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD
        ) {
            $editorClass = ' wysiwyg-standard';
        } elseif ($element instanceof Dbjr_Form_Element_Textarea
            && $element->getWysiwygType() === Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_EMAIL
        ) {
            $editorClass = ' wysiwyg-email';
        } else {
            $editorClass = '';
        }

        $element
            ->setAttrib('class', 'form-control' . $editorClass)
            ->clearDecorators()
            ->addDecorator(
                'Label',
                ['escape' => $this->getOption('escapeLabel') === false ? false : true]
            )
            ->addDecorator('ViewHelper')
            ->addDecorator('Errors')
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
