<?php

class Dbjr_Form_Element_Text extends Zend_Form_Element_Text
{
    const DATEPICKER_TYPE_DATE = 'datepicker';
    const DATEPICKER_TYPE_DATETIME = 'datetimepicker';

    /**
     * Indicates the datepicker type to be used
     * @see  self::DATEPICKER_TYPE_*
     * @var string
     */
    private $_datepickerType;

    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Text
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

    public function setDatepicker($datepickerType)
    {
        if ($datepickerType !== self::DATEPICKER_TYPE_DATETIME
            && $datepickerType !== self::DATEPICKER_TYPE_DATE
        ) {
            throw new Dbjr_Exception('Invalid datepicker type.');
        }

        $this->_datepickerType = $datepickerType;
        return $this;
    }

    public function getDatepicker()
    {
        return $this->_datepickerType;
    }
}
