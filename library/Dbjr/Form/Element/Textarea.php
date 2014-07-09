<?php

class Dbjr_Form_Element_Textarea extends Zend_Form_Element_Textarea
{
    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Textarea
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
