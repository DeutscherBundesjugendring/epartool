<?php

class Admin_View_Helper_EmailSettingsNavigation extends Zend_View_Helper_Abstract
{
    /**
     * Returns consultation secondary navigation html
     * @param  string  $activeItem  Name of the active item
     * @return string               The menu html
     */
    public function emailSettingsNavigation($activeItem = null)
    {
        $items = array(
            array(
                'name' => 'templates',
                'label' => 'Templates',
                'href' => $this->view->url(array('controller' => 'mail-template', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'mail-template', 'action' => 'detail')),
            ),
            array(
                'name' => 'components',
                'label' => 'Components',
                'href' => $this->view->url(array('controller' => 'mail-component', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'mail-component', 'action' => 'detail')),
            ),
        );

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
