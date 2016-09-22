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
                "default-src 'self';
                 media-src *.youtube.com *.ytimg.com *.vimeo.com *.facebook.com *.facebook.net *.twitter.com;
                 font-src gstatic.com
                 script-src *.google.com *.youtube.com *.facebook.com *.doubleclick.net *.twitter.com gstatic.com;"
            )
            ->setHeader('X-Content-Type-Options', "nosniff");
    }
}
