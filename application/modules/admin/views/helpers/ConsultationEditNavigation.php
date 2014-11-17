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
                'name' => 'general',
                'label' => 'General',
                'href' => $this->view->url(array('controller' => 'consultation', 'action' => 'edit', 'kid' => $kid)),
            ),
            array(
                'name' => 'voting',
                'label' => 'Voting',
                'href' => $this->view->url(array('controller' => 'voting', 'action' => 'settings', 'kid' => $kid)),
            ),
            array(
                'name' => 'phases',
                'label' => 'Phases',
                'href' => $this->view->url(array('controller' => 'consultation', 'action' => 'phases', 'kid' => $kid)),
            ),
        );

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
