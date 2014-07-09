<?php

class Dbjr_Form_Element_Date extends Zend_Form_Element_Xhtml
{
    /**
     * Defines default view helper
     * @var string
     */
    public $helper = 'formDate';

    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Date
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
