<?php

use \Facebook\FacebookSession;
use \Facebook\FacebookRequest;

class Service_Webservice_Facebook extends Service_Webservice
{
    const API_VERSION = 'v2.10';
    /**
     * Holds the facebook session
     * @var \Facebook\Authentication\AccessToken
     */
    private $accessToken;

    /**
     * @var \Facebook\Facebook
     */
    private $facebook;

    /**
     * Constructor
     * @param string  $token  The token gotten from Facebook upon user authentication
     */
    public function __construct($token)
    {
        $facebookConf = Zend_Registry::get('systemconfig')->webservice->facebook;
        $this->facebook = new Facebook\Facebook([
            'app_id' => $facebookConf->appId,
            'app_secret' => $facebookConf->appSecret,
            'default_graph_version' => self::API_VERSION,
        ]);
        $this->accessToken = new \Facebook\Authentication\AccessToken($token);
    }

    /**
     * Returns the email of the currently logged in user
     * @return string The users email
     */
    public function getEmail()
    {
        $response = $this->facebook->get('/me?fields=email', $this->accessToken);

        $response = $response->getDecodedBody();

        if (isset($response['email'])) {
            return $response['email'];
        }

        return null;
    }
}
