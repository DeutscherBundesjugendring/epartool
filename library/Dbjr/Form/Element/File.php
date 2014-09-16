<?php

class Dbjr_Form_Element_File extends Zend_Form_Element_File
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
            $this->addDecorator('BootstrapFile');
        }
        return $this;
    }
}
