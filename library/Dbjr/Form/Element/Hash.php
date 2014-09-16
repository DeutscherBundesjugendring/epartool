<?php
class Dbjr_Form_Element_Hash extends Zend_Form_Element_Hash
{
    public function initCsrfValidator()
    {
        $session = $this->getSession();
        if (isset($session->hash)) {
            $rightHash = $session->hash;
        } else {
            $rightHash = null;
        }

        $this->addValidator('Identical', true, array($rightHash));

        $this->getValidator('Identical')->setMessages(
            [
                Zend_Validate_Identical::NOT_SAME => 'Ungültiger Sicherheitstoken',
                Zend_Validate_Identical::MISSING_TOKEN => 'Sicherheitstoken abgelaufen',
            ]
        );

        return $this;
    }

    /**
     * Load default decorators
     * @return Dbjr_Form_Element_File
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
}
