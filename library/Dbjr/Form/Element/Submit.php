<?php

class Dbjr_Form_Element_Submit extends Zend_Form_Element_Submit
{
    /**
     * Loads default decorators
     * @return Dbjr_Form_Element_Submit
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
