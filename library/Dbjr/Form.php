<?php

class Dbjr_Form extends Zend_Form
{
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
