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
        $config = Zend_Registry::get('systemconfig')->cors;

        $header = '';
        foreach ($config as $source => $allowedDomains) {
            $header .= $source . ' ' . implode(' ', $allowedDomains->toArray()) . '; ';
        }

        return $header;
    }
}
