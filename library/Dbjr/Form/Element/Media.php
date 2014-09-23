<?php

class Dbjr_Form_Element_Media extends Zend_Form_Element_Text
{
    /**
     * Holds the consultation identifier
     * @var integer
     */
    private $_kid;

    /**
     * Holds the fodler name
     * @var string
     */
    private $_folder;

    /**
     * Indicates if the media selection popup window should be navigable
     * or if the user is to be locked inside one directory
     * @var boolean
     */
    private $_isLockDir;

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

    public function getIsLockDir()
    {
        return $this->_isLockDir;
    }

    public function setIsLockDir($isLockDir)
    {
        $this->_isLockDir = $isLockDir;
        return $this;
    }

}
