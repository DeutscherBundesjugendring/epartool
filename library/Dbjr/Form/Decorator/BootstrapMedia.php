<?php

class Dbjr_Form_Decorator_BootstrapMedia extends Zend_Form_Decorator_Abstract
{

    public function render($content)
    {
        $imgPath = Zend_Registry::get('baseUrl') . '/media/' . $this->getElement()->getValue();
        if ($this->getElement()->getKid()) {
            $kidFolderPath = '/kid/' . $this->getElement()->getKid();
        } elseif ($this->getElement()->getFolder()) {
            $kidFolderPath = '/folder/' . $this->getElement()->getFolder();
        } else {
            $kidFolderPath = '';
        }

        $element = $this->getElement();
        $element
            ->setAttrib('class', 'form-control')
            ->clearDecorators()
            ->addDecorator(
                'Label',
                ['escape' => $this->getOption('escapeLabel') === false ? false : true]
            )
            ->addDecorator('Errors')
            ->addDecorator('Description', ['tag' => 'p', 'class' => 'help-block'])
            ->addDecorator(
                ['previewImage' => 'HtmlTag'],
                [
                    'tag' => 'img',
                    'src' => $imgPath,
                    'placement' => 'append',
                ]
            )
            ->addDecorator(
                'Callback',
                [
                    'callback'  => function ($content, $element, $options) {
                        return "<a href=\"\" onclick=\"javascript:window.open('{$options['href']}', '_blank', 'width: 500, height: 500'); return false;\">{$options['label']}</a>";
                    },
                    'href'  => Zend_Registry::get('baseUrl') . '/admin/media/index/targetElId/' . $this->getElement()->getId() . $kidFolderPath,
                    'label' => (new Zend_View())->translate('Change image'),
                    'placement' => Zend_Form_Decorator_Abstract::APPEND
                ]
            )
            ->addDecorator(
                ['hiddenInput' => 'HtmlTag'],
                [
                    'tag' => 'input',
                    'id' => $this->getElement()->getId(),
                    'type' => 'hidden',
                    'name' => $this->getElement()->getName(),
                    'value' => $this->getElement()->getValue(),
                ]
            )
            ->addDecorator(
                ['formGroup' => 'HtmlTag'],
                [
                    'tag' => 'div',
                    'id' => ['callback' => [get_class($element), 'resolveElementId']],
                    'class' => 'form-group',
                ]
            );

        return $element->render();
    }
}
