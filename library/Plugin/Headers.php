<?php

class Plugin_Headers extends Zend_Controller_Plugin_Abstract
{
    /**
     * @param \Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->getResponse()
            ->setHeader('Content-Security-Policy', $this->getCORSConfig())
            ->setHeader('X-Content-Type-Options', "nosniff");
    }

    /**
     * @return string
     * @throws \Zend_Exception
     */
    private function getCORSConfig()
    {
        $config = Zend_Registry::get('systemconfig')->cors_default;
        if (!$config) {
            $config = new Zend_Config([]);
        }

        $configLocal = Zend_Registry::get('systemconfig')->cors;
        if (!$configLocal) {
            $configLocal = new Zend_Config([]);
        }

        $keys = array_merge(array_keys($config->toArray()), array_keys($configLocal->toArray()));

        $header = '';
        foreach ($keys as $source) {
            $header .= $source . ' ' . implode(' ', array_merge(
                    $config->get($source, new Zend_Config([]))->toArray(),
                    $configLocal->get($source, new Zend_Config([]))->toArray()
                )) . '; ';
        }

        return $header;
    }
}
