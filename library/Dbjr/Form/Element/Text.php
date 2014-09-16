<?php

class Dbjr_Form_Element_Text extends Zend_Form_Element_Text
{
    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Text
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
