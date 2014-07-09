<?php

class Dbjr_Form_Element_MultiCheckbox extends Zend_Form_Element_MultiCheckbox
{
    /**
     * Load default decorators
     *
     * @return Zend_Form_Element_MultiCheckbox
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $this->addDecorator('BootstrapCheckbox');

        // Disable 'for' attribute
        if (false !== $decorator = $this->getDecorator('label')) {
            $decorator->setOption('disableFor', true);
        }

        return $this;
    }
}
