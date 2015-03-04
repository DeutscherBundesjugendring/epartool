<?php

class Dbjr_Form_Element_Password extends Zend_Form_Element_Password
{
    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Password
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
