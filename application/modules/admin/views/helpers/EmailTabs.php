<?php

class Admin_View_Helper_EmailTabs extends Zend_View_Helper_Abstract
{
    /**
     * Returns consultation tab navigation html
     * @param  string  $activeItem  Name of the active item
     * @return string               The menu html
     */
    public function emailTabs($activeItem = null)
    {
        $items = [
            [
                'name' => 'emailing',
                'href' => $this->view->url(array('controller' => 'mail-sent', 'action' => 'index')),
                'label' => '<span class="glyphicon glyphicon-envelope "></span> Emailing',
            ],
            [
                'name' => 'settings',
                'href' => $this->view->url(array('controller' => 'mail-template', 'action' => 'index')),
                'label' => '<span class="glyphicon glyphicon-cog"></span> Settings',
            ]

        ];

        return $this->view->tabsNavigation($items, $activeItem);
    }
}
