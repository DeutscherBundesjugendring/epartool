<?php

class Dbjr_Form_Element_Multiselect extends Zend_Form_Element_Multiselect
{
    /**
     * Indicates if element should be displayd as select2
     * @var boolean
     */
    private $isSelect2;

    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Select
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
     * @param $isSelect2
     * @return $this
     */
    public function setIsSelect2($isSelect2)
    {
        $this->isSelect2 = (bool) $isSelect2;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsSelect2()
    {
        return $this->isSelect2;
    }
}
