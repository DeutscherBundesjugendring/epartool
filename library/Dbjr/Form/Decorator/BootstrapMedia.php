<?php

class Dbjr_Form_Decorator_BootstrapMedia extends Zend_Form_Decorator_Abstract
{

    public function render($content)
    {
        $element = $this->getElement();

        if ($element->getKid()) {
            $kidFolderParam = '/kid/' . $element->getKid();
        } elseif ($element->getFolder()) {
            $kidFolderParam = '/folder/' . $element->getFolder();
        } else {
            $kidFolderParam = '';
        }
        $lockDir = $element->getIsLockDir() ? '/lockDir/1' : '';
        if ($element->getKid()) {
            $imgPath = MEDIA_PATH . '/' . Service_Media::MEDIA_DIR_CONSULTATIONS . '/' . $element->getKid() . '/' . $element->getValue();
        } else {
            $imgPath = MEDIA_PATH . '/' . $element->getValue();
        }

        $origClass = $element->getAttrib('class') ? ' ' . $element->getAttrib('class') : '';
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
                    'src' => (new Application_View_Helper_MediaPresenter())->mediaPresenter($imgPath, 'admin_media_form_element_lowDPI'),
                    'placement' => 'append',
                ]
            )
            ->addDecorator(
                ['changeBtn' => 'Callback'],
                [
                    'callback'  => function ($content, $element, $options) {
                        $html = <<<EOD
<a href="#" onclick="javascript:window.open('{$options['href']}', '_blank', 'width: 500, height: 500'); return false;">
    {$options['label']}
</a>
EOD;
                        return $html;
                    },
                    'href' => Zend_Registry::get('baseUrl') . '/admin/media/index/targetElId/' . $element->getId() . $kidFolderParam . $lockDir,
                    'label' => (new Zend_View())->translate('Change media'),
                    'placement' => Zend_Form_Decorator_Abstract::APPEND
                ]
            );

            if ($element->getIsRemovable()) {
                $element->addDecorator(
                    ['removeBtn' => 'Callback'],
                    [
                        'callback'  => function ($content, $element, $options) {
                            $html = <<<EOD
<a href="#" onclick="javascript:$(this).parent('div').remove(); return false;">
    {$options['label']}
</a>
EOD;
                            return $html;
                        },
                        'label' => (new Zend_View())->translate('Remove media'),
                        'placement' => Zend_Form_Decorator_Abstract::APPEND
                    ]
                );
            }

            $element->addDecorator(
                ['hiddenInput' => 'HtmlTag'],
                array_merge(
                    [
                        'tag' => 'input',
                        'id' => $element->getId(),
                        'type' => 'hidden',
                        'name' => ($element->getBelongsTo() ? $element->getBelongsTo() . '[' : '') . $element->getName() . ($element->getBelongsTo() ? ']' : ''),
                        'value' => $element->getValue(),
                    ],
                    $element->getAttrib('disabled') ? ['disabled' => null] : []
                )
            )
            ->addDecorator(
                ['formGroup' => 'HtmlTag'],
                [
                    'tag' => 'div',
                    'id' => ['callback' => [get_class($element), 'resolveElementId']],
                    'class' => 'form-group' . $origClass,
                ]
            );

        return $element->render();
    }
}
