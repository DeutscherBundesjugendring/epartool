<?php

class Module_Default_View_Helper_ThemeAssets extends Zend_View_Helper_Abstract
{
    /**
     * @var array
     */
    private $assets;
    
    public function themeAssets()
    {
        if ($this->assets === null) {
            $project = (new Model_Projects())->find(Zend_Registry::get('systemconfig')->project)->current();
            $this->assets = [
                'logo' => $project['logo'],
                'favicon' => $project['favicon'],
                'mitmachen_bubble' => (bool) $project['mitmachen_bubble'],
            ];
        }
        
        return $this->assets;
    }
}
