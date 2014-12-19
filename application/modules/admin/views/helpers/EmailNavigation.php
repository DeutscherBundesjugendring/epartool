<?php

class Admin_View_Helper_EmailNavigation extends Zend_View_Helper_Abstract
{
    /**
     * Returns consultation secondary navigation html
     * @param  string  $activeItem  Name of the active item
     * @return string               The menu html
     */
    public function emailNavigation($activeItem = null)
    {
        $items = array(
            array(
                'name' => 'sent',
                'label' => $this->view->translate('Sent'),
                'href' => $this->view->url(array('controller' => 'mail-sent', 'action' => 'index')),
            ),
            array(
                'name' => 'queued',
                'label' => $this->view->translate('Queued'),
                'href' => $this->view->url(array('controller' => 'mail-queued', 'action' => 'index')),
            ),
        );

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
