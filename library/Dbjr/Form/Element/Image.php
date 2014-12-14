<?php

class Dbjr_Form_Element_Image extends Zend_Form_Element_Image
{
    /**
     * Load default decorators
     *
     * @return Dbjr_Form_Element_Image
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this
            ->addDecorator('Label')
            ->addDecorator('Image')
            ->addDecorator('Errors')
            ->addDecorator(
                'HtmlTag',
                [
                    'tag' => 'div',
                    'id' => ['callback' => [get_class($this), 'resolveElementId']],
                    'class' => 'form-group',
                ]
            );
        }
        return $this;
    }
}
