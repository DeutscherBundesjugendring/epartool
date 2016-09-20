<?php

class Plugin_Headers extends Zend_Controller_Plugin_Abstract
{
    /**
     * @param \Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->getResponse()
            ->setHeader('Content-Security-Policy', "default-src 'self'; media-src youtube.com vimeo.com facebook.com;");
    }
}
