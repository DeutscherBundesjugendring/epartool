<?php

class Dbjr_Form_DisplayGroup extends Zend_Form_DisplayGroup
{
    /**
     * Load default decorators
     * @return Zend_Form_DisplayGroup
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                 ->addDecorator('Fieldset');
        }

        return $this;
    }

}
