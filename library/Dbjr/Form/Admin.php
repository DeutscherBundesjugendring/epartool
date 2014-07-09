<?php

class Dbjr_Form_Admin extends Dbjr_Form
{
    /**
     * Default display group class
     * @var string
     */
    protected $_defaultDisplayGroupClass = 'Zend_Form_DisplayGroup';

    public function __construct($options = null)
    {
        $this->addElementPrefixPath('Dbjr_Form', 'Dbjr/Form/');
        $this->addPrefixPath('Dbjr_Form', 'Dbjr/Form/');
        parent::__construct($options);
    }

    /**
     * Load the default decorators
     * @return Zend_Form
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this
                ->addDecorator('FormElements')
                ->addDecorator('Form', ['role' => 'form']);
        }

        return $this;
    }
}
