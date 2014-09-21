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
                'href' => '/admin/media/index',
            ),
            array(
                'name' => 'consultations',
                'label' => 'Consultations',
                'href' => '/admin/media/' . Service_Media::MEDIA_DIR_CONSULTATIONS,
            ),
            array(
                'name' => 'folders',
                'label' => 'Folders',
                'href' => '/admin/media/' . Service_Media::MEDIA_DIR_FOLDERS,
                'new_item' => $this->view->url(array('controller' => 'media', 'action' => 'create-folder')),
            ),
        );

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
