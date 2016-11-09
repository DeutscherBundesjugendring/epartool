<?php

namespace Component;

class LoginComponent
{
    public static $loginButtonId = '#loginDropdown';
    public static $fieldUsernameId = '#username';
    public static $fieldPasswordId = '#password';
    public static $loginSubmitButtonId = '#submit';
    public static $menuDropDown = '.navbar-toggle';
    public static $userDropDown = '#menu .navbar-right .dropdown .dropdown-toggle';
    public static $logOffIcon = '.glyphicon-off';
    public static $logOffLink = 'Log off';
    public static $loginFailedMessage = 'Login failed!';
    public static $loginInvalidCredentialsMessage = 'Supplied credential is invalid.';
}
