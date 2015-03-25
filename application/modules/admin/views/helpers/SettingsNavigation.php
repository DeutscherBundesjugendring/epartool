<?php

class Admin_View_Helper_SettingsNavigation extends Zend_View_Helper_Abstract
{
    /**
     * Returns consultation secondary navigation html
     * @param  string  $activeItem  Name of the active item
     * @return string               The menu html
     */
    public function settingsNavigation($activeItem = null)
    {
        $items = array(
            array(
                'name' => 'site',
                'label' => $this->view->translate('Site'),
                'href' => $this->view->url(array('controller' => 'settings', 'action' => 'index')),
            ),
            array(
                'name' => 'pages',
                'label' => $this->view->translate('Pages'),
                'href' => $this->view->url(array('controller' => 'article', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'article', 'action' => 'create')),
            ),
            array(
                'name' => 'keywords',
                'label' => $this->view->translate('Keywords'),
                'href' => $this->view->url(array('controller' => 'tag', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'tag', 'action' => 'create')),
            ),
            array(
                'name' => 'helpTexts',
                'label' => $this->view->translate('Help Texts'),
                'href' => $this->view->url(array('controller' => 'settings', 'action' => 'help-text-index')),
            ),
            array(
                'name' => 'partners',
                'label' => $this->view->translate('Partners'),
                'href' => $this->view->url(['controller' => 'settings', 'action' => 'partner-index', 'id' => null]),
                'new_item' => $this->view->url(['controller' => 'settings', 'action' => 'partner-edit', 'id' => null]),
            ),
        );

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
