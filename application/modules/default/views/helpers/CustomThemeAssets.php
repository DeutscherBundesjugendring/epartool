<?php

class Module_Default_View_Helper_CustomThemeAssets extends Zend_View_Helper_Abstract
{
    /**
     * @var array
     */
    private $assets;
    
    public function customThemeAssets()
    {
        if ($this->assets === null) {
            $project = (new Model_Projects())->find(Zend_Registry::get('systemconfig')->project)->current();
            $this->assets = ['logo' => $project['logo'], 'favicon' => $project['favicon']];
        }
        
        return $this->assets;
    }
}
