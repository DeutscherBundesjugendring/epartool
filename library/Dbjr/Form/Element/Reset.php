<?php

class Dbjr_Form_Element_Reset extends Zend_Form_Element_Reset
{
    /**
     * Loads default decorators
     * @return Dbjr_Form_Element_Reset
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('ViewHelper');
            $this->setAttrib('class', 'btn btn-primary');
        }

        return $this;
    }
}
