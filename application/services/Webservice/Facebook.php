<?php

use \Facebook\FacebookSession;
use \Facebook\FacebookRequest;

class Service_Webservice_Facebook extends Service_Webservice
{
    /**
     * Holds the facebook session
     * @var FacebookSession
     */
    private $_session;

    /**
     * Constructor
     * @param string  $token  The token gotten from Facebook upon user authentication
     */
    public function __construct($token)
    {
        $facebookConf = Zend_Registry::get('systemconfig')->webservice->facebook;
        FacebookSession::setDefaultApplication($facebookConf->appId, $facebookConf->appSecret);
        $this->_session = new FacebookSession($token);
    }

    /**
     * Returns the email of the currently logged in user
     * @return string The users email
     */
    public function getEmail()
    {
        $request = new FacebookRequest($this->_session, 'GET', '/me');
        $response = $request->execute()->getGraphObject();

        return $response->getProperty('email');
    }
}
