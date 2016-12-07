<?php

/**
 * Class Admin_View_Helper_ToolVersion
 */
class Application_View_Helper_ToolVersion extends Zend_View_Helper_Abstract
{
    const VERSION_FILE = 'VERSION.txt';

    /**
     * @var string
     */
    private $versionInfo;

    /**
     * @return string
     */
    public function toolVersion()
    {
        if ($this->versionInfo === null) {
            $versionFilePath = APPLICATION_PATH . '/../' . self::VERSION_FILE;
            if (!file_exists($versionFilePath) || !($content = file_get_contents($versionFilePath))) {
                trigger_error('No valid file VERSION.txt found in app root.', E_USER_WARNING);
                $this->versionInfo = 0;

                return $this->versionInfo;
            }

            $this->versionInfo = trim($content);
        }

        return $this->versionInfo;
    }
}
