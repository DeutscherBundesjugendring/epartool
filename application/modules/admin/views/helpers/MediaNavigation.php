<?php

class Admin_View_Helper_MediaNavigation extends Zend_View_Helper_Abstract
{
    /**
     * Returns consultation secondary navigation html
     * @param  string  $activeItem  Name of the active item
     * @return string               The menu html
     */
    public function mediaNavigation($activeItem = null)
    {
        $items = array(
            array(
                'name' => 'all',
                'label' => 'All Media',
                'href' => $this->view->url(array('controller' => 'media', 'action' => 'index')),
            ),
            array(
                'name' => 'categories',
                'label' => 'Categories',
                'href' => $this->view->url(array('controller' => 'media', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'media', 'action' => 'index')),
            ),
        );

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
