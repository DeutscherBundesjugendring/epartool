<?php

namespace Page\Front\Contribution;

class ConfirmContributionPage
{
    public static $url = '';
    
    public static $fieldEmailId = '#email';
    public static $checkboxAgree = '#is_contrib_under_cc';
    public static $buttonSendId = '#submit';
    
    public static $messageInfo =
        'Please fill in the form in order to confirm your contributions to this participation round!';
    public static $messageEmailSent =
        'An email for the confirmation of your contributions has been sent to your email address.';
}
