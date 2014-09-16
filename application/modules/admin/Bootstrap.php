<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initNavigation()
    {
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/modules/admin/config/navigation.ini');
        $nav = $view->navigation(new Zend_Navigation($config));
        $nav->menu()->setUlClass("nav navbar-nav");
    }
}
