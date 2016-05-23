<?php

/**
 * Class Admin_View_Helper_ToolVersion
 */
class Application_View_Helper_ToolVersion extends Zend_View_Helper_Abstract
{
    /**
     * @return string
     */
    public function toolVersion()
    {
        $version = (new Zend_Registry())->get('systemconfig')->version;
        return $version !== null ? $version : 0;
    }
}
