<?php

class Model_HelpText extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'help_text';
    protected $_primary = 'id';

    /**
     * Returns translated name of the help text
     * @param  string  $name  The untranslated name
     * @return string         The translated name
     */
    public static function getTranslatedName($name)
    {
        $trans = Zend_Registry::get('Zend_Translate');
        $names = [
            'help-text-home' => $trans->translate('Home page help text'),
            'help-text-consultation-info' => $trans->translate('Consultation info help text'),
            'help-text-consultation-question' => $trans->translate('Consultation question help text'),
            'help-text-consultation-input' => $trans->translate('Consultation input help text'),
            'help-text-consultation-voting' => $trans->translate('Consultation voting help text'),
            'help-text-consultation-followup' => $trans->translate('Consultation reactions & impact help text'),
            'help-text-login' => $trans->translate('Login help text'),

            'help-text-admin-consultation-voting-preparation' => $trans->translate('Voting preparation help text'),
            'help-text-admin-consultation-voting-permissions' => $trans->translate('Voting permissions help text'),
            'help-text-admin-consultation-voting-invitations' => $trans->translate('Voting invitations help text'),
            'help-text-admin-consultation-voting-participants' => $trans->translate('Voting participants help text'),
            'help-text-admin-consultation-voting-results' => $trans->translate('Voting results help text'),
            'help-text-admin-consultation-follow-up' => $trans->translate('Reactions & Impact help text'),
            'help-text-admin-consultation-follow-up-snippets' => $trans->translate('Follow up snippets help text'),

            'help-text-admin-consultation-settings-general' =>
                $trans->translate('Consultation general settings help text'),
            'help-text-admin-consultation-settings-participants-data' =>
                $trans->translate('Consultation participant settings help text'),
            'help-text-admin-consultation-settings-voting' =>
                $trans->translate('Consultation voting settings help text'),
            'help-text-admin-consultation-settings-phases' =>
                $trans->translate('Consultation phase settings help text'),
            'help-text-admin-consultation-settings-groups' =>
                $trans->translate('Consultation group settings help text'),

            'help-text-admin-contribution' => $trans->translate('Contribution help text'),
            'help-text-admin-question' => $trans->translate('Question help text'),
        ];

        return $names[$name];
    }
}
