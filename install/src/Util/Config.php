<?php

namespace Util;

use Zend_Config;
use Zend_Config_Writer_Ini;

class Config
{
    private $writer;

    /**
     * Config constructor.
     * @param \Zend_Config_Writer_Ini $writer
     * @param string $configPath
     */
    public function __construct(Zend_Config_Writer_Ini $writer, $configPath)
    {
        $this->writer = $writer;
        $this->configPath = $configPath;
    }

    /**
     * @param string $subDir
     * @param array $db
     * @param array $email
     * @param string $projectCode
     * @param string $cronKey
     * @param string $securityToken
     * @param string $googleId
     * @param string $googleSecret
     * @param string $facebookId
     * @param string $facebookSecret
     * @param string $vimeoAccessToken
     */
    public function writeConfigLocalIni(
        $subDir,
        array $db,
        array $email,
        $projectCode,
        $cronKey,
        $securityToken,
        $googleId,
        $googleSecret,
        $facebookId,
        $facebookSecret,
        $vimeoAccessToken
    ) {
        $confLocalIni = new Zend_Config([], true);
        $confLocalIni->production = [];
        $confLocalIni->development = [];
        $confLocalIni->setExtend('development', 'production');

        $confLocalIni->production->project = $projectCode;
        $confLocalIni->production->resources = [];
        $confLocalIni->production->resources->db = [];
        $confLocalIni->production->resources->db->params = [];
        $confLocalIni->production->resources->db->params->host = $db['host'];
        $confLocalIni->production->resources->db->params->dbname = $db['name'];
        $confLocalIni->production->resources->db->params->username = $db['userName'];
        $confLocalIni->production->resources->db->params->password = $db['password'];

        $confLocalIni->production->resources->mail = [];
        $confLocalIni->production->resources->mail->defaultFrom = [];
        $confLocalIni->production->resources->mail->defaultFrom->email = $email['fromAddress'];
        $confLocalIni->production->resources->mail->defaultFrom->name = $email['fromName'];
        $confLocalIni->production->resources->mail->defaultReplyTo = [];
        $confLocalIni->production->resources->mail->defaultReplyTo->email = $email['replyToAddress']
            ? $email['replyToAddress']
            : $email['fromAddress'];
        $confLocalIni->production->resources->mail->defaultReplyTo->name = $email['replyToName']
            ? $email['replyToName']
            : $email['fromName'];

        if (!empty($email['smtp']['auth'])) {
            $confLocalIni->production->resources->mail->transport = [];
            $confLocalIni->production->resources->mail->transport->type = 'smtp';
            $confLocalIni->production->resources->mail->transport->auth = $email['smtp']['auth'];
            $confLocalIni->production->resources->mail->transport->port = $email['smtp']['port'];
            if (!empty($email['smtp']['ssl'])) {
                $confLocalIni->production->resources->mail->transport->ssl = $email['smtp']['ssl'];
            }
            $confLocalIni->production->resources->mail->transport->host = $email['smtp']['host'];
            $confLocalIni->production->resources->mail->transport->password = $email['smtp']['password'];
            $confLocalIni->production->resources->mail->transport->username = $email['smtp']['username'];
        }
        $confLocalIni->production->cron = [];
        $confLocalIni->production->cron->key = $cronKey;
        $confLocalIni->production->security = [];
        $confLocalIni->production->security->token = $securityToken;
        $confLocalIni->production->webservice = [];
        $confLocalIni->production->webservice->google = [];
        $confLocalIni->production->webservice->google->clientId = $googleId;
        $confLocalIni->production->webservice->google->clientSecret = $googleSecret;
        $confLocalIni->production->webservice->facebook = [];
        $confLocalIni->production->webservice->facebook->appId = $facebookId;
        $confLocalIni->production->webservice->facebook->appSecret = $facebookSecret;
        $confLocalIni->production->webservice->vimeo = [];
        $confLocalIni->production->webservice->vimeo->accessToken = $vimeoAccessToken;
        $confLocalIni->production->resources->frontController = [];
        $confLocalIni->production->resources->frontController->baseUrl = $subDir;
        $this->writer->write($this->configPath . '/config.local.ini', $confLocalIni);
    }
}
