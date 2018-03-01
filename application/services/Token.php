<?php

class Service_Token
{
    /**
     * @var Zend_Session_Namespace
     */
    private $session;

    public function __construct()
    {
        $this->session = new Zend_Session_Namespace('actionToken');
    }

    /**
     * @return string
     */
    public function get(): string
    {
        $this->session->token = md5(uniqid());

        return $this->session->token;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function verify(string $token): bool
    {
        if ($this->session->token === $token) {
            $this->session->token = null;

            return true;
        }

        return false;
    }
}
