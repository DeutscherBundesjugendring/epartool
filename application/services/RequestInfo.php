<?php

class Service_RequestInfo
{
    const SECURED_PORT = 443;

    /**
     * @return bool
     */
    public function isSecure() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || $_SERVER['SERVER_PORT'] === self::SECURED_PORT;
    }
}
