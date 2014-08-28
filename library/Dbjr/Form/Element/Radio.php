<?php

class Dbjr_Form_Element_Radio extends Zend_Form_Element_Radio
{
    /**
     * Load default decorators
     *
     * Disables "for" attribute of label if label decorator enabled.
     *
     * @return Zend_Form_Element_Radio
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $this->addDecorator('BootstrapMulti');

        // Disable 'for' attribute
        if (isset($this->_decorators['Label'])
            && !isset($this->_decorators['Label']['options']['disableFor']))
        {
             $this->_decorators['Label']['options']['disableFor'] = true;
        }

        return $this;
    }
}
