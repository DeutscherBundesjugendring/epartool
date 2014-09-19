<?php

class Dbjr_Form_Element_Button extends Zend_Form_Element_Button
{
    /**
     * Loads default decorators
     * @return Dbjr_Form_Element_Button
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('ViewHelper');
            if (!$this->getAttrib('class')) {
                $this->setAttrib('class', 'btn btn-primary');
            }
        }

        return $this;
    }
}
