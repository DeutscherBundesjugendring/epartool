<?php

namespace Util;

use Zend_Layout;
use Zend_Registry;
use Zend_Translate;
use Zend_Validate_Abstract;
use Zend_View;

class View
{
    private $view;
    private $layout;

    /**
     * View constructor.
     * @param string $layoutPath
     * @param string $scriptPath
     * @param string $helperPath
     * @param string $zendLangPath
     * @param string $langPath
     * @param string $locale
     */
    public function __construct($layoutPath, $scriptPath, $helperPath, $zendLangPath, $langPath, $locale = null)
    {
        $this->layout = (new Zend_Layout())
            ->setLayoutPath($layoutPath)
            ->setLayout('base');

        $this->view = (new Zend_View())
            ->addScriptPath($scriptPath)
            ->addHelperPath($helperPath, 'Application_View_Helper');

        if ($locale) {
            $this->setTranslator($locale, $zendLangPath, $langPath);
        }
    }

    /**
     * @param array $params
     * @throws \Zend_View_Exception
     */
    public function assign(array $params)
    {
        $this->view->assign($params);
    }

    /**
     * @param string $template
     */
    public function render($template)
    {
        $this->layout->content = $this->view->render($template);
        echo $this->layout->render();

        exit;
    }

    /**
     * @param string $locale
     * @param string $zendLangPath
     * @param string $langPath
     * @throws \Zend_Validate_Exception
     */
    private function setTranslator($locale, $zendLangPath, $langPath)
    {
        $translator = new Zend_Translate([
            'adapter' => 'array',
            'content' => $zendLangPath,
            'scan' => Zend_Translate::LOCALE_DIRECTORY,
            'locale' => $locale,
        ]);
        Zend_Validate_Abstract::setDefaultTranslator($translator);

        $translator = new Zend_Translate([
            'adapter' => 'Gettext',
            'content' => $langPath,
            'scan' => Zend_Translate::LOCALE_FILENAME,
            'locale' => $locale,
        ]);
        Zend_Registry::set('Zend_Translate', $translator);

        $this->view->getHelper('Translate')->setTranslator($translator);
    }

    /**
     * @return \Zend_View_Abstract
     */
    public function getZendView()
    {
        return $this->view;
    }
}
