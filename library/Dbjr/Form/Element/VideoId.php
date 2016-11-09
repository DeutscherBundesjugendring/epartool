<?php

class Dbjr_Form_Element_VideoId extends Dbjr_Form_Element_Text
{
    public function __construct($spec, $options)
    {
        $this->setDisableLoadDefaultDecorators(true);
        parent::__construct($spec, $options);

        $this
            ->setAttrib(
                'class',
                'form-control'
                . ($this->getAttrib('class') ? ' ' . $this->getAttrib('class') : '')
            )
            ->clearDecorators()
            ->addDecorator(
                'Label',
                ['escape' => (!isset($options['escapeLabel']) || $options['escapeLabel'] === false) ? false : true]
            )
            ->addDecorator('ViewHelper')
            ->addDecorator(
                'Description',
                [
                    'tag' => 'p',
                    'class' => 'help-block',
                    'escape' => (!isset($options['escapeLabel']) || $options['escapeDescription'] === false)
                        ? false
                        : true
                ]
            )
            ->addDecorator(
                'HtmlTag',
                [
                    'tag' => 'div',
                    'id' => ['callback' => [get_class($this), 'resolveElementId']],
                    'class' => 'form-group',
                ]
            );
    }

    public function init()
    {
        $this->addValidator(new Dbjr_Validate_VideoValidator());
    }
}
