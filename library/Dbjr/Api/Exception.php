<?php

class Dbjr_Api_Exception extends Dbjr_Exception
{
    /**
     * @var int
     */
    private $httpStatusCode;

    /**
     * Dbjr_Api_Exception constructor.
     * @param int $httpStatusCode
     * @param string $message
     */
    public function __construct($httpStatusCode, $message)
    {
        parent::__construct($message);
        $this->httpStatusCode = $httpStatusCode;
    }


    /**
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }
}
