<?php

class Dbjr_Form_Element_Time extends Zend_Form_Element_Xhtml
{
    /**
     * Use formTime view helper by default
     * @var string
     */
    public $helper = 'formTime';

    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Time
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
