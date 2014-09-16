<?php

class Dbjr_Form_Element_Select extends Zend_Form_Element_Select
{
    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Select
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('BootstrapStandard');
        }
        return $this;
    }
}
