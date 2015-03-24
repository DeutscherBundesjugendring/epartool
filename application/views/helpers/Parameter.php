<?php

class Application_View_Helper_Parameter extends Zend_View_Helper_Abstract
{
    /**
     * Holds the site settings as a cache
     * @var array
     */
    private $_params;

    /**
     * Returns the value of the specified parameter
     * @param  string $paramName    The name of the parameter
     * @return string               The value of the parameter
     */
    public function parameter($paramName)
    {
        // The parameters are often needed all in one request and therefore we want to cache them.
        if (!$this->_params) {
            $this->_params = (new Model_Parameter())->getAsArray();
        }

        if (!isset($this->_params[$paramName])) {
            return null;
        }

        return $this->view->escape($this->_params[$paramName]);
    }
}
