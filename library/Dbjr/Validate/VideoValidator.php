<?php

class Dbjr_Validate_VideoValidator extends Zend_Validate_Abstract
{
    const API_UNAVAILABLE = 'api_unavailable';
    const NOT_PUBLIC = 'not_public';
    
    protected $_messageTemplates = [
        self::API_UNAVAILABLE => 'Video provider is not currently available. Video could not be verified for accessibility.',
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

        $accessToken = $this->sendRequest('https://graph.facebook.com/oauth/access_token', [
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'grant_type' => 'client_credentials',
        ]);

        if ($accessToken['httpCode'] !== 200) {
            $this->_error($this->_messageTemplates[self::API_UNAVAILABLE]);

            return false;
        }
        
        $jsonResponse = json_decode($accessToken['output'], true);
        if ($jsonResponse && isset($jsonResponse['error'])) {
            $this->_error($this->_messageTemplates[self::API_UNAVAILABLE]);

            return false;
        }

        $response = $this->sendRequest('https://graph.facebook.com/v2.6/' . $id . '?' . $accessToken['output']);

        if ($response['httpCode'] === 200) {
            $jsonResponse = json_decode($response['output'], true);
            if ($jsonResponse && isset($jsonResponse['id'])) {
                return true;
            }
        }
        $this->_error($this->_messageTemplates[self::NOT_PUBLIC]);

        return false;
    }
    
    /**
     * @param string $id
     * @return boolean
     */
    private function validateVimeoVideo($id)
    {
        $accessToken = Zend_Registry::get('systemconfig')->webservice->vimeo->accessToken;

        $response = $this->sendRequest('https://api.vimeo.com/videos/' . $id, ['access_token' => $accessToken]);
        if ($response['httpCode'] === 200) {
            $jsonResponse = json_decode($response['output'], true);
            if ($jsonResponse && isset($jsonResponse['privacy']) && isset($jsonResponse['privacy']['embed'])
                && mb_strpos('public', $jsonResponse['privacy']['embed']) !== false
                && mb_strpos('anybody', $jsonResponse['privacy']['view']) !== false) {

                return true;
            }
            $this->_error($this->_messageTemplates[self::NOT_PUBLIC]);

            return false;
        }
        $this->_error($this->_messageTemplates[self::API_UNAVAILABLE]);

        return false;
    }
    
    /**
     * @param string $id
     * @return boolean
     */
    private function validateYoutubeVideo($id)
    {
        $response = $this->sendRequest('https://www.youtube.com/oembed', [
            'url' => 'http://www.youtube.com/watch?v=' . $id,
            'format' => 'json',
        ]);
        
        if ($response['httpCode'] === 200) {
            return true;
        } elseif ($response['httpCode'] === 401) {
            $this->_error($this->_messageTemplates[self::NOT_PUBLIC]);

            return false;
        }
        $this->_error($this->_messageTemplates[self::API_UNAVAILABLE]);

        return false;
    }

    /**
     * @param $url
     * @param array $params
     * @return array
     */
    private function sendRequest($url, array $params = [])
    {
        $encoded = '';
        foreach ($params as $name => $value) {
            $encoded .= urlencode($name).'='.urlencode($value).'&';
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url . ($encoded !== '' ? '?' . $encoded : ''));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $output = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return ['httpCode' => $httpCode, 'output' => $output];
    }
}
