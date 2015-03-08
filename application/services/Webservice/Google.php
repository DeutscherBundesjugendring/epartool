<?php

class Service_Webservice_Google extends Service_Webservice
{
    /**
     * Holds the google SDK client
     * @var Google_Client
     */
    private $_client;

    /**
     * Constructor
     * @param string  $token  The token gotten from Google upon user authentication
     */
    public function __construct($token)
    {
        $googleConf = Zend_Registry::get('systemconfig')->webservice->google;
        $this->_client = new Google_Client();
        $this->_client->setClientId($googleConf->clientId);
        $this->_client->setClientSecret($googleConf->clientSecret);
        $this->_client->setRedirectUri('postmessage');
        $this->_client->setScopes('email', 'profile');
        $this->_client->authenticate($token);
    }

    /**
     * Returns the email of the currently logged in user
     * @return string The users email
     */
    public function getEmail()
    {
        $token = json_decode($this->_client->getAccessToken())->access_token;
        $req = new Google_Http_Request('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $token);
        $tokenInfo = json_decode($this->_client->getAuth()->authenticatedRequest($req)->getResponseBody());

        if (!empty($tokenInfo->error)) {
            throw new Exception($tokenInfo->error);
        }

        if ($tokenInfo->audience !== Zend_Registry::get('systemconfig')->webservice->google->clientId) {
            throw new Exception('invalid audience.');
        }

        return $tokenInfo->email;
    }
}
