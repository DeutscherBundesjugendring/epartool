<?php

class Dbjr_Form_Element_Submit extends Zend_Form_Element_Submit
{
    /**
     * Loads default decorators
     * @return Dbjr_Form_Element_Submit
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('ViewHelper');
        }

        return $this;
    }

    public function render(Zend_View_Interface $view = null)
    {
        $origCssClass = $this->getAttrib('class') ? ' ' . $this->getAttrib('class') : '';
        $this->setAttrib('class', 'btn' . $origCssClass);

        return parent::render($view);
    }
}
