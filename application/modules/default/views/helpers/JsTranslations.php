<?php

class Module_Default_View_Helper_JsTranslations extends Zend_View_Helper_Abstract
{

    /**
     * @return array
     */
    public function jsTranslations()
    {
        $translator = Zend_Registry::get('Zend_Translate');
        return [
            'password_strength_weak' => $translator->translate('Weak'),
            'password_strength_normal' => $translator->translate('Normal'),
            'password_strength_medium' => $translator->translate('Medium'),
            'password_strength_strong' => $translator->translate('Strong'),
            'password_strength_very_strong' => $translator->translate('Very Strong'),
            'label_shut_back' => $translator->translate('Shut back'),
            'label_explain_contribution' => $translator->translate('Click here to explain contribution'),
            'label_supporters' => $translator->translate('supporters'),
            'label_loading' => $translator->translate('Loading…'),
            'message_logging_in' => $translator->translate('You are being logged in. Please wait…'),
            'message_general_error' => $translator->translate('Something went wrong'),
            'message_contributions_save_error' => $translator->translate('Your contributions have not been saved'),
            'navigator_geolocation_not_available' => $translator->translate('Geolocation is not available in your browser'),
        ];
    }
}
