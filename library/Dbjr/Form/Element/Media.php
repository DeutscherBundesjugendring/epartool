<?php

class Dbjr_Form_Element_Media extends Zend_Form_Element_Text
{
    private $_kid;
    private $_folder;

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
            $this->addDecorator('BootstrapMedia');
        }
        return $this;
    }

    public function getKid()
    {
        return $this->_kid;
    }

    public function setKid($kid)
    {
        $this->_kid = $kid;
        return $this;
    }

    public function getFolder()
    {
        return $this->_folder;
    }

    public function setFolder($folder)
    {
        $this->_folder = $folder;
        return $this;
    }
}
