<?php

class Dbjr_Form_Element_Search extends Zend_Form_Element_Xhtml
{
    /**
     * Defines default view helper
     * @var string
     */
    public $helper = 'formSearch';

    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Search
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
