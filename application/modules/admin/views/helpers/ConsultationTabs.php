<?php

class Admin_View_Helper_ConsultationTabs extends Zend_View_Helper_Abstract
{
    /**
     * Returns consultation tab navigation html
     * @param  integer $kid         Identifier of the consultation
     * @param  string  $activeItem  Name of the active item
     * @return string               The menu html
     */
    public function consultationTabs($kid, $activeItem = null)
    {
        $items = [
            [
                'name' => 'consultation',
                'href' => $this->view->url(array('controller' => 'consultation', 'action' => 'index', $kid)),
                'icon' => '<span class="glyphicon glyphicon-folder-close"></span>',
                'label' => 'Consultation',
            ],
            [
                'name' => 'settings',
                'href' => $this->view->url(array('controller' => 'consultation', 'action' => 'edit', $kid)),
                'icon' => '<span class="glyphicon glyphicon-cog"></span>',
                'label' => 'Settings',
            ]

        ];

        return $this->view->tabsNavigation($items, $activeItem);
    }
}
