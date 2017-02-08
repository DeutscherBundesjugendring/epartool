<?php

namespace Form;

use Dbjr_Form_Web;
use Zend_Registry;

class InstallForm extends Dbjr_Form_Web
{

    public function init()
    {
        $trans = Zend_Registry::get('Zend_Translate');



        $dbHost = $this->createElement('text', 'dbHost');
        $dbHost
            ->setLabel('Hostname')
            ->setRequired(true);
        $this->addElement($dbHost);

        $dbName = $this->createElement('text', 'dbName');
        $dbName
            ->setLabel('Database Name')
            ->setRequired(true);
        $this->addElement($dbName);

        $dbUsername = $this->createElement('text', 'dbUsername');
        $dbUsername
            ->setLabel('Username')
            ->setRequired(true);
        $this->addElement($dbUsername);

        $dbPass = $this->createElement('text', 'dbPass');
        $dbPass
            ->setLabel('Password')
            ->setRequired(true);
        $this->addElement($dbPass);

        $this->addDisplayGroup(
            ['dbHost', 'dbName', 'dbUsername', 'dbPass'],
            'dbSettings',
            ['legend' => $trans->translate('Database'), 'class' => 'offset-bottom']
        );



        $locale = $this->createElement('select', 'locale');
        $locale
            ->setLabel('Language')
            ->setMultioptions(['en_US' => $trans->translate('English'), 'de_DE' => $trans->translate('German')]);
        $this->addElement($locale);

        $cronKey = $this->createElement('text', 'cronKey');
        $cronKey
            ->setLabel('Cron key')
            ->setDescription('Random string used in the cronjob url.')
            ->setRequired(true);
        $this->addElement($cronKey);

        $this->addDisplayGroup(
            ['locale', 'cronKey'],
            'toolSettings',
            ['legend' => $trans->translate('Application'), 'class' => 'offset-bottom']
        );



        $emailFromAddress = $this->createElement('email', 'emailFromAddress');
        $emailFromAddress
            ->setLabel('Email From Address')
            ->setRequired(true);
        $this->addElement($emailFromAddress);

        $emailFromName = $this->createElement('text', 'emailFromName');
        $emailFromName
            ->setLabel('Email From Name')
            ->setRequired(true);
        $this->addElement($emailFromName);

        $emailReplyToAddress = $this->createElement('email', 'emailReplyToAddress');
        $emailReplyToAddress
            ->setLabel('Email Reply To Address')
            ->setDescription('If left blank, the value of "From" address will be used.');
        $this->addElement($emailReplyToAddress);

        $emailReplyToName = $this->createElement('text', 'emailReplyToName');
        $emailReplyToName
            ->setLabel('Email Reply To Name')
            ->setDescription('If left blank, the value of "From" name will be used.');
        $this->addElement($emailReplyToName);

        $this->addDisplayGroup(
            ['emailFromAddress', 'emailFromName', 'emailReplyToAddress', 'emailReplyToName'],
            'emailSettings',
            ['legend' => $trans->translate('System Email'), 'class' => 'offset-bottom']
        );



        $emailSmtpAuth = $this->createElement('select', 'emailSmtpAuth');
        $emailSmtpAuth
            ->setLabel('Auth Type')
            ->setMultiOptions([
                '' => $trans->translate('Please select...'),
                'plain' => 'Plain',
                'login' => 'Login',
                'crammd5' => 'CRAM-MD5'
            ]);
        $this->addElement($emailSmtpAuth);

        $emailSmtpPort = $this->createElement('number', 'emailSmtpPort');
        $emailSmtpPort
            ->setLabel('Port');
        $this->addElement($emailSmtpPort);

        $emailSmtpSsl = $this->createElement('select', 'emailSmtpSsl');
        $emailSmtpSsl
            ->setLabel('Encryption')
            ->setMultiOptions([
                '' => $trans->translate('None'),
                'ssl' => 'SSL',
                'tls' => 'TLS',
            ]);
        $this->addElement($emailSmtpSsl);

        $emailSmtpHost = $this->createElement('text', 'emailSmtpHost');
        $emailSmtpHost
            ->setLabel('Host');
        $this->addElement($emailSmtpHost);

        $emailSmtpUserName = $this->createElement('text', 'emailSmtpUserName');
        $emailSmtpUserName
            ->setLabel('Username');
        $this->addElement($emailSmtpUserName);

        $emailSmtpPass = $this->createElement('password', 'emailSmtpPass');
        $emailSmtpPass
            ->setLabel('Password');
        $this->addElement($emailSmtpPass);

        $this->addDisplayGroup(
            ['emailSmtpAuth', 'emailSmtpPort', 'emailSmtpSsl', 'emailSmtpHost', 'emailSmtpUserName', 'emailSmtpPass'],
            'smtpSettings',
            ['legend' => $trans->translate('Smtp Settings (If unsure, leave blank)'), 'class' => 'offset-bottom']
        );



        $facebookId = $this->createElement('text', 'facebookId');
        $facebookId
            ->setLabel('Facebook App ID')
            ->setDescription(
                'Facebook App ID is generated by Facebook when a new application is created.'
                . 'To create a Facebook application please visit '
                . '<a href="https://developers.facebook.com" target="_blank">https://developers.facebook.com</a>.'
            );
        $facebookId->getDecorator('BootstrapStandard')->setOption('escapeDescription', false);
        $this->addElement($facebookId);


        $facebookSecret = $this->createElement('text', 'facebookSecret');
        $facebookSecret
            ->setLabel('Facebook App Secret')
            ->setDescription(
                'Facebook App Secret is generated by Facebook when a new application is created.'
                . 'To create a Facebook application please visit '
                . '<a href="https://developers.facebook.com" target="_blank">https://developers.facebook.com</a>.'
            );
        $facebookSecret->getDecorator('BootstrapStandard')->setOption('escapeDescription', false);
        $this->addElement($facebookSecret);

        $googleId = $this->createElement('text', 'googleId');
        $googleId
            ->setLabel('Google Client ID')
            ->setDescription(
                'Google Client ID is generated by Google when a new project is created.'
                . 'To create a Google project please visit '
                . '<a href="https://console.developers.google.com" target="_blank">https://console.developers.google.com</a>.'
            );
        $googleId->getDecorator('BootstrapStandard')->setOption('escapeDescription', false);
        $this->addElement($googleId);

        $googleSecret = $this->createElement('text', 'googleSecret');
        $googleSecret
            ->setLabel('Google Client Secret')
            ->setDescription(
                'Google Client Secret is generated by Google when a new project is created.'
                . 'To create a Google project please visit '
                . '<a href="https://console.developers.google.com" target="_blank">https://console.developers.google.com</a>.'
            );
        $googleSecret->getDecorator('BootstrapStandard')->setOption('escapeDescription', false);
        $this->addElement($googleSecret);

        $vimeoAccessToken = $this->createElement('text', 'vimeoAccessToken');
        $vimeoAccessToken
            ->setLabel('Vimeo Access Token')
            ->setDescription(
                'Vimeo access token is generated when a new application is created.'
                . 'To create a vimeo access token please visit '
                . '<a href="https://developer.vimeo.com/apps" target="_blank">https://developer.vimeo.com/apps</a>.'
            );
        $vimeoAccessToken->getDecorator('BootstrapStandard')->setOption('escapeDescription', false);
        $this->addElement($vimeoAccessToken);
        
        $this->addDisplayGroup(
            ['facebookId', 'facebookSecret', 'googleId', 'googleSecret', 'vimeoAccessToken'],
            'webserviceSettings',
            ['legend' => $trans->translate('Social network login'), 'class' => 'offset-bottom']
        );



        $adminName = $this->createElement('text', 'adminName');
        $adminName
            ->setLabel('Name')
            ->setRequired(true);
        $this->addElement($adminName);

        $adminEmail = $this->createElement('email', 'adminEmail');
        $adminEmail
            ->setLabel('Email')
            ->setRequired(true);
        $this->addElement($adminEmail);

        $this->addDisplayGroup(
            ['adminEmail', 'adminName'],
            'adminSettings',
            ['legend' => $trans->translate('Administrator'), 'class' => 'offset-bottom']
        );



        $hash = $this->createElement('hash', 'csrf_token_instalation', ['salt' => 'unique']);
        $hash
            ->setSalt(md5(mt_rand(1, 100000) . time()))
            ->setTimeout(360);
        $this->addElement($hash);

        $submit = $this->createElement('submit', 'submit');
        $submit
            ->setLabel('Install')
            ->setAttrib('class', 'btn-primary');
        $this->addElement($submit);
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Zend_Form_Exception
     */
    public function isValid($data)
    {
        if (!empty($data['emailSmtpAuth'])
            || !empty($data['emailSmtpPort'])
            || !empty($data['emailSmtpSsl'])
            || !empty($data['emailSmtpHost'])
            || !empty($data['emailSmtpPass'])
            || !empty($data['emailSmtpUserName'])
        ) {
            $this->getElement('emailSmtpHost')->setRequired(true);
            $this->getElement('emailSmtpPort')->setRequired(true);
        }

        return parent::isValid($data);
    }
}
