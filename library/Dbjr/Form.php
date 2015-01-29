<?php

class Dbjr_Form extends Zend_Form
{
    /**
     * Default display group class
     * @var string
     */
    protected $_defaultDisplayGroupClass = 'Zend_Form_DisplayGroup';

    /**
     * Holds the info about the cancel link
     *  [
     *      url => relative url including baseUrl to where cancel link is to point
     *      label => the translated label to be uused as link text, optional, defaults to translation of "Cancel"
     *      prefix => translated link prefix, optional, defaults to translation of "or"
     *      blockClass => class to be applied to the div wrapper, optional, defaults to "cancel-link"
     *  ]
     * If null no cancel link will be output
     * @var array
     */
    protected $_cancelLink;

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
            $this->addDecorator('FormElements');
            if ($this->_cancelLink) {
                $this->addDecorator(
                    'cancelLink',
                    [
                        'url' => $this->_cancelLink['url'],
                        'label' => isset($this->_cancelLink['label']) ? $this->_cancelLink['label'] : null,
                        'prefix' => isset($this->_cancelLink['prefix']) ? $this->_cancelLink['prefix'] : null,
                        'blockClass' => isset($this->_cancelLink['blockClass']) ? $this->_cancelLink['blockClass'] : null,
                    ]
                );
            }
            $this->addDecorator('Form', ['role' => 'form']);
        }

        return $this;
    }

    /**
     * Getter for the self::$_cancelLink property
     * @return array The cancel link data @see self::$_cancelLink for format
     */
    public function getCancelLink()
    {
        return $this->_cancelLink;
    }

    /**
     * Setter for the self::$_cancelLink property
     * @param   array      $cancelLink The cancel link data @see self::$_cancelLink for format
     * @return  Dbjr_Form              Fluent interface
     */
    public function setCancelLink($cancelLink)
    {
        $this->_cancelLink = $cancelLink;

        return $this;
    }

    /**
     * Adds a css class to the element
     * This is needed as the Dbjr_Form_Decorator_* add their own classes and we do not want to overwrite them
     * It is sa static method becuse there is no way to have a common ancestor to all Dbjr custom form elements
     * @param   Zend_Form_Element   $element    The element to which the class is to be added
     * @param   string              $class      The class to be added
     */
    public static function addCssClass($element, $class)
    {
        $oldClass = $element->getAttrib('class') ? ' ' . $element->getAttrib('class') : '';
        $element->setAttrib('class', $class . $oldClass);
    }
}
