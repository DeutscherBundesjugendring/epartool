<?php

class Dbjr_Form_Element_Hidden extends Zend_Form_Element_Hidden
{
    /**
     * Load default decorators
     * @return Dbjr_Form_Element_File
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('ViewHelper');
        }
        return $this;
    }
}
