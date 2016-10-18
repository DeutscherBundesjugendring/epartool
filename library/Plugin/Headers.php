<?php

class Plugin_Headers extends Zend_Controller_Plugin_Abstract
{
    /**
     * @param \Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->getResponse()
            ->setHeader(
                'Content-Security-Policy',
                "default-src 'self' *.google.com *.facebook.com *.youtube.com; "
                . "media-src 'self' 'unsafe-inline' 'unsafe-eval' *.youtube.com *.ytimg.com *.vimeo.com *.facebook.com "
                . "*.facebook.net *.twitter.com; "
                . "img-src 'self' data: *.facebook.com;"
                . "font-src 'self' *.gstatic.com *.facebook.com *.googleapis.com;"
                . "script-src 'self' 'unsafe-inline' 'unsafe-eval' *.google.com *.youtube.com *.facebook.com "
                . "*.facebook.net *.doubleclick.net *.twitter.com *.gstatic.com; "
                . "style-src 'self' 'unsafe-inline' 'unsafe-eval' *.googleapis.com;"
                . "connect-src 'self';"
            )
            ->setHeader('X-Content-Type-Options', "nosniff");
    }
}
