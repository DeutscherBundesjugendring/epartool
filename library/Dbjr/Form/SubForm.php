<?php

class Dbjr_Form_SubForm extends Zend_Form_SubForm
{
    /**
     * Load the default decorators
     * @return Zend_Form_SubForm
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
