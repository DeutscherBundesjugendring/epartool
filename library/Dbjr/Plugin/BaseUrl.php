<?php
/**
 * Registers baseUrl including scheme and host in the registry,
 * used in links in emails
 * @author Markus Hackel
 *
 */
class Plugin_BaseUrl extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $host = '';
        $baseUrl = '';
        if ($request instanceof Zend_Controller_Request_Http) {
            $host = $request->getHttpHost();
            $baseUrl = $request->getScheme() . '://' . $host . $request->getBaseUrl();
        }
        Zend_Registry::set('httpHost', $host);
        Zend_Registry::set('baseUrl', $baseUrl);
    }
}
