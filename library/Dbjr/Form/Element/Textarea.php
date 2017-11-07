<?php

class Dbjr_Form_Element_Textarea extends Zend_Form_Element_Textarea
{

    const WYSIWYG_TYPE_STANDARD = 'standard';
    const WYSIWYG_TYPE_EMAIL = 'email';

    /**
     * @var int
     */
    private $consultationId;

    /**
     * Indicates what type, if any, should by associated with this element
     * @see  self::WYSIWYG_TYPE_*
     * @var string
     */
    private $wysiwygType;

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

    /**
     * @param string $wysiwygType
     * @return $this
     * @throws Dbjr_Exception
     */
    public function setWysiwygType($wysiwygType, $consultationId = null)
    {
        if ($wysiwygType !== self::WYSIWYG_TYPE_STANDARD
            && $wysiwygType !== self::WYSIWYG_TYPE_EMAIL
        ) {
            throw new Dbjr_Exception('Invalid wysiwyg type.');
        }

        $this->wysiwygType = $wysiwygType;
        $this->consultationId = $consultationId;
        return $this;
    }

    /**
     * @return string
     */
    public function getWysiwygType()
    {
        return $this->wysiwygType;
    }

    /**
     * @return bool
     */
    public function isWysiwygType()
    {
        return ($this->wysiwygType !== null);
    }

    /**
     * @return int
     */
    public function getConsultationId()
    {
        return $this->consultationId;
    }

    /**
     * @return string
     */
    public function basePathToPlaceholder()
    {
        return (new Service_Wysiwyg((new Zend_View_Helper_BaseUrl())->getBaseUrl()))
            ->basePathToPlaceholder($this->getValue());
    }

    public function placeholderToBasePath()
    {
        $this->setValue((new Service_Wysiwyg((new Zend_View_Helper_BaseUrl())->getBaseUrl()))
            ->placeholderToBasePath($this->_value)
        );
    }
}
