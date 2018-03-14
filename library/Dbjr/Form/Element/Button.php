<?php

class Dbjr_Form_Element_Button extends Zend_Form_Element_Button
{
    const TYPE_DELETE = 'delete';

    /**
     * Indicates the action the button is to trigger
     * @see  slef::TYPE_*
     * @var string
     */
    private $_actionType;

    /**
     * Indicates if the button should trigger a confirmation dialog
     * @var boolean
     */
    private $_isConfirm;

    /**
     * The message to be used by the confirmation dialog if enabled
     * @var string
     */
    private $_confirmMessage;

    /**
     * Loads default decorators
     * @return Dbjr_Form_Element_Button
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

    public function setActionType($actionType)
    {
        if ($actionType !== self::TYPE_DELETE) {
            throw new Dbjr_Exception('Invalid submit button type requested.');
        }

        $this->_actionType = $actionType;
        return $this;
    }

    public function setIsConfirm($isConfirm)
    {
        $this->_isConfirm = (bool) $isConfirm;
        return $this;
    }

    public function setConfirmMessage($confirmMessage)
    {
        $this->_confirmMessage = $confirmMessage;
        return $this;
    }

    public function render(Zend_View_Interface $view = null)
    {
        if ($this->_actionType === self::TYPE_DELETE) {
            $newCssClass = 'item-action item-action-danger';
            $this
                ->setOptions(['escape' => false, 'type' => 'submit'])
                ->setLabel('<span class="glyphicon glyphicon-trash"></span>')
                ->setIsConfirm(true);
        } else {
            $newCssClass = 'btn';
        }


        if ($this->_isConfirm) {
            $this->setAttrib('data-toggle', 'confirm');
        }
        if ($this->_confirmMessage) {
            $translator = Zend_Registry::get(('Zend_Translate'));
            $this->setAttrib('data-confirm-message', $translator->translate($this->_confirmMessage));
            $this->setAttrib('data-confirm-yes', $translator->translate('Yes'));
            $this->setAttrib('data-confirm-no', $translator->translate('No'));
        }

        $origCssClass = $this->getAttrib('class') ? ' ' . $this->getAttrib('class') : '';
        $this->setAttrib('class', $newCssClass . $origCssClass);

        return parent::render($view);
    }
}
