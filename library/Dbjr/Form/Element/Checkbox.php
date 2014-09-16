<?php

class Dbjr_Form_Element_Checkbox extends Zend_Form_Element_Checkbox
{
    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Checkbox
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('BootstrapCheckbox');
        }
        return $this;
    }
}
