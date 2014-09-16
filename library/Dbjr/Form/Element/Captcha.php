<?php

class Dbjr_Form_Element_Captcha extends Zend_Form_Element_Captcha
{
    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Captcha
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
