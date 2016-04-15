<?php

class Dbjr_Form_Decorator_BootstrapStandard extends Zend_Form_Decorator_Abstract
{
    /**
     * @param string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $element
            ->setAttrib(
                'class',
                'form-control'
                . ($element->getAttrib('class') ? ' ' . $element->getAttrib('class') : '')
            )
            ->clearDecorators()
            ->addDecorator(
                'Label',
                ['escape' => $this->getOption('escapeLabel') === false ? false : true]
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
                'HtmlTag',
                [
                    'tag' => 'div',
                    'id' => ['callback' => [get_class($element), 'resolveElementId']],
                    'class' => 'form-group',
                ]
            );

        $this->setDatePickerLoader($element);
        $this->setSelect2Loader($element);
        $this->setWysiwygLoader($element);

        return $element->render();
    }

    /**
     * @param  Zend_Form_Element $element
     */
    private function setDatePickerLoader($element)
    {
        if ($element instanceof Dbjr_Form_Element_Text) {
            if ($element->getDatepicker() === 'datetimepicker') {
                $element->setAttrib('data-onload-datetimepicker', '{
                    "format": "YYYY-MM-DD HH:mm:ss",
                    "sideBySide": true,
                    "locale": "' . (new Zend_Locale())->getLanguage()  . '"
                }');
            } elseif ($element->getDatepicker() === 'datepicker') {
                $element->setAttrib('data-onload-datetimepicker', '{
                    "format": "YYYY-MM-DD",
                    "sideBySide": true,
                    "locale": "' . (new Zend_Locale())->getLanguage()  . '"
                }');
            }
        }
    }

    /**
     * @param  Zend_Form_Element $element
     */
    private function setWysiwygLoader($element)
    {
        if ($element instanceof Dbjr_Form_Element_Textarea) {
            $baseUrl = Zend_Layout::getMvcInstance()->getView()->baseUrl();
            if ($element->getWysiwygType() === Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_STANDARD) {
                $element->setAttrib('data-onload-ckeditor', json_encode([
                    'customConfig' => $baseUrl . '/js/ckeditor.web_config.js',
                    'filebrowserBrowseUrl' => $baseUrl . '/admin/media/index/targetElId/CKEditor'
                ]));
            } elseif ($element->getWysiwygType() === Dbjr_Form_Element_Textarea::WYSIWYG_TYPE_EMAIL) {
                $element->setAttrib('data-onload-ckeditor', json_encode([
                    'customConfig' => $baseUrl . '/js/ckeditor.email_config.js',
                ]));
            }
        }
    }

    /**
     * @param  Zend_Form_Element $element
     */
    private function setSelect2Loader($element)
    {
        if ($element instanceof Dbjr_Form_Element_Multiselect && $element->getIsSelect2()) {
            $element->setAttrib('data-onload-select2', '{}');
        }
    }
}
