<?php

class Dbjr_Validate_VideoValidator extends Zend_Validate_Abstract
{
    const API_UNAVAILABLE = 'api_unavailable';
    const NOT_PUBLIC = 'not_public';
    
    protected $_messageTemplates = [
        self::API_UNAVAILABLE => 'Video could not be verified for accessibility.',
        self::NOT_PUBLIC => 'Video is not public.',
    ];
    
    /**
     * @param string $value
     * @param array $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        if (isset($context['video_service']) && isset($context['video_id'])) {
            if ($context['video_service'] === 'youtube') {
                return $this->validateYoutubeVideo($value);
            } elseif ($context['video_service'] === 'vimeo') {
                return $this->validateVimeoVideo($value);
            } elseif ($context['video_service'] === 'facebook') {
                return $this->validateFacebookVideo($value);
            }
        }
        
        return true;
    }
    
    /**
     * @param string $id
     * @return boolean
     */
    private function validateFacebookVideo($id)
    {
        $appId = Zend_Registry::get('systemconfig')->webservice->facebook->appId;
        $appSecret = Zend_Registry::get('systemconfig')->webservice->facebook->appSecret;
        
        $accessToken = @file_get_contents('https://graph.facebook.com/oauth/access_token?client_id=' . $appId
            . '&client_secret=' . $appSecret . '&grant_type=client_credentials');

        if ($accessToken === FALSE) {
            $this->_error(self::API_UNAVAILABLE);
            return true;
        }
        
        $jsonResponse = json_decode($accessToken, true);
        if ($jsonResponse && isset($jsonResponse['error'])) {
            $this->_error(self::API_UNAVAILABLE);
            return true;
        }
        
        $response = @file_get_contents('https://graph.facebook.com/v2.6/' . $id . '?' . $accessToken);

        if ($response !== FALSE) {
            $jsonResponse = json_decode($response, true);
            if ($jsonResponse && isset($jsonResponse['id'])) {
                return true;
            }
        }
        
        $this->_error(self::NOT_PUBLIC);
        return false;
    }
    
    /**
     * @param string $id
     * @return boolean
     */
    private function validateVimeoVideo($id)
    {
        $accessToken = Zend_Registry::get('systemconfig')->webservice->vimeo->accessToken;
        $response = @file_get_contents('https://api.vimeo.com/videos/' . $id . '?access_token='.$accessToken);
        if ($response !== FALSE) {
            $jsonResponse = json_decode($response, true);
            if ($jsonResponse && isset($jsonResponse['privacy']) && isset($jsonResponse['privacy']['embed'])
                && strpos('public', $jsonResponse['privacy']['embed']) !== false
                && strpos('anybody', $jsonResponse['privacy']['view']) !== false) {
                return true;
            }
        }
        
        $this->_error(self::NOT_PUBLIC);
        return false;
    }
    
    /**
     * @param string $id
     * @return boolean
     */
    private function validateYoutubeVideo($id)
    {
        $response = @file_get_contents('https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=' . $id . '&format=json');
        if ($response === FALSE) {
            $this->_error(self::NOT_PUBLIC);
            return false;
        }
        return true;
    }
}
