<?php

abstract class Service_Webservice
{
    /**
     * Constructor
     * @param string  $token  The token gotten from the webservice upon user authentication
     */
    abstract public function __construct($token);

    /**
     * Returns the email of the currently logged in user
     * @return string The users email
     */
    abstract public function getEmail();
}
