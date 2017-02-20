<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initConfig()
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/modules/default/config/config.ini',
            APPLICATION_ENV
        );
        Zend_Registry::set('config', $config);

        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/config.ini',
            APPLICATION_ENV,
            array('allowModifications' => true)
        );

        if (is_file(APPLICATION_PATH . '/configs/config.local.ini')) {
            $configLocal = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/config.local.ini'
            );
            $env = APPLICATION_ENV;
            if (isset($configLocal->$env)) {
                $config->merge($configLocal->$env);
            }
        }

        Zend_Registry::set('systemconfig', $config);
    }

    protected function _initActionHelpers()
    {
        Zend_Controller_Action_HelperBroker::addPrefix('Dbjr_Controller_Action_Helper');
    }

    protected function _initDefaultModuleAutoloader()
    {
        $resourceLoader = new Zend_Application_Module_Autoloader(
            array(
                'namespace' => '',
                'basePath'    => APPLICATION_PATH,
            )
        );

        return $resourceLoader;
    }

    protected function _initRegistry()
    {
        // Initialisierung des Db-Adapters erzwingen
        $this->bootstrap('db');
        $registry = Zend_Registry::getInstance();
        $config = new Zend_Config($this->getOptions());
        $registry->configuration = $config;
        $registry->dbAdapter = $this->getResource('db');

        return $registry;
    }

    protected function _initSessions()
    {
        $this->bootstrap('session');
        Zend_Session::setOptions(['cookie_httponly' => true]);
    }

    protected function _initCache()
    {
        $manager = $this
            ->getPluginResource('cachemanager')
            ->getCacheManager();
        $cache = $manager->getCache('database');
        Zend_Locale::setCache($cache);
    }

    protected function _initAuth()
    {
        $this->bootstrap('frontController');
        $auth = Zend_Auth::getInstance();
        $acl = new Plugin_Auth_Acl();
        $this->getResource('frontController')
            ->registerPlugin(new Plugin_Auth_AccessControl($auth, $acl))
            ->setParam('auth', $auth);
    }

    protected function _initLog()
    {
        if ($this->hasPluginResource("log")) {
            $r = $this->getPluginResource("log");
            $log = $r->getLog();

            Zend_Registry::set("log", $log);
        }
    }

    protected function _initHead()
    {
        $view = $this->bootstrap('view')->getResource('view');
    }

    protected function _initHeaders()
    {
        $this->bootstrap('frontController');
        $this->getResource('frontController')->registerPlugin(new Plugin_Headers());
    }

    /**
     * Registers the complete URL including protocol and host in the registry,
     * used for links in emails
     */
    protected function _initBaseUrl()
    {
        $this->bootstrap('frontController');
        $request = $this->getResource('frontController')
            ->registerPlugin(new Plugin_BaseUrl());
    }

    protected function _initMessenger()
    {
        $this->bootstrap('frontController');
        $this->getResource('frontController')
            ->registerPlugin(new Plugin_Messenger());
    }

    /**
     * Initialize locale
     */
    protected function _initLocale()
    {
        Zend_Registry::set(
            'Zend_Locale',
            new Zend_Locale(
                (new Model_Projects())->find(Zend_Registry::get('systemconfig')->project)->current()['locale']
            )
        );
    }


    /**
     * Initialize translations
     */
    protected function _initTranslation()
    {
        $translator = new Zend_Translate([
            'adapter' => 'array',
            'content' => VENDOR_PATH . '/zendframework/zendframework1/resources/languages',
            'scan' => Zend_Translate::LOCALE_DIRECTORY,
            'disableNotices' => true,
        ]);
        $translatorAdditionalLanguages = new Zend_Translate([
            'adapter' => 'array',
            'content' => APPLICATION_PATH . '/../languages_zend',
            'scan' => Zend_Translate::LOCALE_DIRECTORY,
        ]);

        $translator->addTranslation(array('content' => $translatorAdditionalLanguages));
        Zend_Validate_Abstract::setDefaultTranslator($translator);
    }
}
