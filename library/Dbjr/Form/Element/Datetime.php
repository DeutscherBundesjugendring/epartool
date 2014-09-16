<?php

class Dbjr_Form_Element_Datetime extends Zend_Form_Element_Xhtml
{
    /**
     * Defines default view helper
     * @var string
     */
    public $helper = 'formDatetime';

    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Datetime
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
