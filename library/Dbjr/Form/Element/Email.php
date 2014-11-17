<?php

class Dbjr_Form_Element_Email extends Zend_Form_Element_Xhtml
{
    /**
     * Defines default view helper
     * @var string
     */
    public $helper = 'formEmail';

    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Email
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

    /**
     * Ensure that value is email
     * @param  string $value
     * @param  mixed $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        $this->addValidator('EmailAddress');
        return parent::isValid($value, $context);
    }
}
