<?php

class Dbjr_Form_Element_Textarea extends Zend_Form_Element_Textarea
{

    const WYSIWYG_TYPE_STANDARD = 'standard';
    const WYSIWYG_TYPE_EMAIL = 'email';

    /**
     * Indicates what type, if any, should by associated with this element
     * @see  self::WYSIWYG_TYPE_*
     * @var string
     */
    private $_wysiwygType;

    /**
     * Load default decorators
     * @return Dbjr_Form_Element_Textarea
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

    public function setWysiwygType($wysiwygType)
    {
        if ($wysiwygType !== self::WYSIWYG_TYPE_STANDARD
            && $wysiwygType !== self::WYSIWYG_TYPE_EMAIL
        ) {
            throw new Dbjr_Exception('Invalid wysiwyg type.');
        }

        $this->_wysiwygType = $wysiwygType;
        return $this;
    }

    public function getWysiwygType()
    {
        return $this->_wysiwygType;
    }
}
