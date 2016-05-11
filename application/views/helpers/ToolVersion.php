<?php

/**
 * Class Admin_View_Helper_ToolVersion
 */
class Application_View_Helper_ToolVersion extends Zend_View_Helper_Abstract
{
    const COMPOSER_FILE = 'composer.json';

    /**
     * @return string
     */
    public function toolVersion()
    {
        $composerInfo = json_decode(file_get_contents(APPLICATION_PATH . '/../' . self::COMPOSER_FILE));
        return isset($composerInfo->version) ? $composerInfo->version : '';
    }
}
