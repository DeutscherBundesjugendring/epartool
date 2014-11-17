<?php

class Dbjr_Form_Decorator_BootstrapStandard extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();
        $element
            ->setAttrib(
                'class',
                'form-control'
                    . $this->getWysiwygCssClass($element)
                    . $this->getDatepickerCssClass($element)
                    . $this->getSelect2CssClass($element)
            )
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

    /**
     * Returns the datepicker css class of the element
     * @param  Zend_Form_Element $element The form element object
     * @return string                     The css class prefixed with empty space.
     *                                    Empty string if not applicable
     */
    private function getDatepickerCssClass($element)
    {
        if ($element instanceof Dbjr_Form_Element_Text && $element->getDatepicker()) {
            return ' js-' . $element->getDatepicker();
        } else {
            return '';
        }
    }

    /**
     * Returns the wysiwyg css class of the element
     * @param  Zend_Form_Element $element The form element object
     * @return string                     The css class prefixed with empty space.
     *                                    Empty string if not applicable
     */
    private function getWysiwygCssClass($element)
    {
        if ($element instanceof Dbjr_Form_Element_Textarea
            && $element->getWysiwygType() === Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD
        ) {
            return ' wysiwyg-standard';
        } elseif ($element instanceof Dbjr_Form_Element_Textarea
            && $element->getWysiwygType() === Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_EMAIL
        ) {
            return ' wysiwyg-email';
        } else {
            return '';
        }
    }

    /**
     * Returns the select2 css class of the element
     * @param  Zend_Form_Element $element The form element object
     * @return string                     The css class prefixed with empty space.
     *                                    Empty string if not applicable
     */
    private function getSelect2CssClass($element)
    {
        if ($element instanceof Dbjr_Form_Element_Multiselect && $element->getIsSelect2()) {
            return ' js-select2';
        } else {
            return '';
        }
    }
}
