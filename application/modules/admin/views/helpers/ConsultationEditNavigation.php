<?php

class Admin_View_Helper_ConsultationEditNavigation extends Zend_View_Helper_Abstract
{
    /**
     * Returns consultation secondary navigation html
     * @param  integer $kid         Identifier of the consultation
     * @param  string  $activeItem  Name of the active item
     * @return string               The menu html
     */
    public function consultationEditNavigation($kid, $activeItem = null)
    {
        $items = array(
            array(
                'name' => 'settings',
                'label' => 'Settings',
                'href' => $this->view->url(array('controller' => 'consultation', 'action' => 'edit', 'kid' => $kid)),
            ),
            // TODO DBJR-94
            /*array(
                'name' => 'phases',
                'label' => 'Phase Names',
                'href' => $this->view->url(array('controller' => 'consultation', 'action' => 'edit', 'kid' => $kid)),
            ),*/
        );

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
