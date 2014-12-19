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
                'label' => $this->view->translate('All Media'),
                'href' => $this->view->url(
                    [
                        'controller' => 'media',
                        'action' => 'index',
                        'targetElId' => $this->view->targetElId,
                        'kid' => null,
                        'folder' => null,
                        'filename' => null,
                    ]
                ),
            ),
            array(
                'name' => 'consultations',
                'label' => $this->view->translate('Consultations'),
                'href' => $this->view->url(
                    [
                        'controller' => 'media',
                        'action' => 'consultations',
                        'targetElId' => $this->view->targetElId,
                        'folder' => null,
                        'filename' => null,
                    ]
                ),
            ),
            array(
                'name' => 'folders',
                'label' => $this->view->translate('Folders'),
                'href' => $this->view->url(
                    [
                        'controller' => 'media',
                        'action' => 'folders',
                        'targetElId' => $this->view->targetElId,
                        'kid' => null,
                        'filename' => null,
                    ]
                ),
                'new_item' => $this->view->url(
                    [
                        'controller' => 'media',
                        'action' => 'create-folder',
                        'targetElId' => $this->view->targetElId,
                        'kid' => null,
                        'folder' => null,
                        'filename' => null,
                    ]
                ),
            ),
        );

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
