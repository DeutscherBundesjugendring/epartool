<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initConfig()
    {
        // Lade Frontend Konfiguration
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/modules/default/config/config.ini',
            APPLICATION_ENV
        );

        // Speichere Frontend Konfiguration in der Registry
        Zend_Registry::set('config', $config);

        // Lade System-Konfiguration
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/config.ini',
            APPLICATION_ENV
        );

        // Speichere System-Konfiguration in der Registry
        Zend_Registry::set('systemconfig', $config);
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

/*
    protected function _initTitle()
    {
        $view = $this->bootstrap('view')->getResource('view');
        $view->headTitle()->setSeparator(' - ');

        $sysconfig = Zend_Registry::get('systemconfig');
        if ($sysconfig->headTitle) {
            $view->headTitle($sysconfig->headTitle);
        } else {
            $view->headTitle('Strukturierter Dialog in Deutschland');
        }
    }

*/
    protected function _initHead()
    {
        $view = $this->bootstrap('view')->getResource('view');
            $view->headTitle()->setSeparator(' - ');

        $sysconfig = Zend_Registry::get('systemconfig');
            if ($sysconfig->headTitle) {
                    $view->headTitle($sysconfig->headTitle);
            } else {
                $view->headTitle('Strukturierter Dialog in Deutschland');
            }
        #$view->headLink()->appendStylesheet('/Pfad/zur Stylesheet/datei');
        $view-> headScript() -> appendFile('http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
    }

    protected function _initSetupBaseUrl()
    {
        $this->bootstrap('frontcontroller');
        $controller = Zend_Controller_Front::getInstance();
        $sysconfig = Zend_Registry::get('systemconfig');
        $subdir = $sysconfig->installation->subdir;
        if (!empty($subdir)) {
            if (substr($subdir, 0, 1) != '/') {
                $subdir.= '/' . $subdir;
            }
            $controller->setBaseUrl($subdir);
        }
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
}
