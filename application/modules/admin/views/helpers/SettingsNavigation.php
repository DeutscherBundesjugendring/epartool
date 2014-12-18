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
            /*array(
                'name' => 'site',
                'label' => 'Site',
                'href' => $this->view->url(array('controller' => 'settings', 'action' => 'index')),
            ),*/
            array(
                'name' => 'pages',
                'label' => 'Pages',
                'href' => $this->view->url(array('controller' => 'article', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'article', 'action' => 'create')),
            ),
            array(
                'name' => 'keywords',
                'label' => 'Keywords',
                'href' => $this->view->url(array('controller' => 'tag', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'tag', 'action' => 'create')),
            ),
            /*array(
                'name' => 'header',
                'label' => 'Header',
                'href' => $this->view->url(array('controller' => 'settings', 'action' => 'index')),
            ),
            array(
                'name' => 'footer',
                'label' => 'Footer',
                'href' => $this->view->url(array('controller' => 'settings', 'action' => 'index')),
            ),*/
        );

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
