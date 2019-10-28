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

    public function __construct(
        string $layoutPath,
        string $scriptPath,
        string $helperPath,
        string $zendLangPath,
        string $langPath,
        ?string $locale = null
    ) {
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

    public function assign(array $params)
    {
        $this->view->assign($params);
    }

    public function render(string $template)
    {
        $this->layout->content = $this->view->render($template);
        echo $this->layout->render();

        exit;
    }

    private function setTranslator(string $locale, string $zendLangPath, string $langPath)
    {
        $translator = new Zend_Translate([
            'adapter' => 'array',
            'content' => $zendLangPath,
            'disableNotices' => true,
            'scan' => Zend_Translate::LOCALE_DIRECTORY,
            'locale' => $locale,
        ]);
        Zend_Validate_Abstract::setDefaultTranslator($translator);

        $translator = new Zend_Translate([
            'adapter' => 'Gettext',
            'content' => $langPath,
            'disableNotices' => true,
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
