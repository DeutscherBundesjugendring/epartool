<?php
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initNavigation()
    {
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/modules/admin/config/navigation.ini');

        $container = new Zend_Navigation($config);

        // Get Consultations for Navigation
        $consultationModel = new Model_Consultations();
        $pages = $consultationModel->getNavigationEntries();

        // Add Consultation pages to page "Konsultationen"
        foreach ($pages AS $page) {
            $page = new Zend_Navigation_Page_Mvc($page);
            $container->findOneBy('label', 'Beteiligungsrunden')->addPage($page);
        }

        // Set container in navigation view helper
        $nav = $view->navigation($container);
        // Set css class
        $nav->menu()->setUlClass("vlist");

        // Register plugin for setting active consultation in navigation
        $fc = Zend_Controller_Front::getInstance();
        $fc->registerPlugin(new Plugin_Navigation());
    }

}
