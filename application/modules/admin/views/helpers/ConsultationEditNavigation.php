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
        $items = [
            [
                'name' => 'general',
                'label' => $this->view->translate('General'),
                'href' => $this->view->url(['controller' => 'consultation', 'action' => 'edit', 'kid' => $kid]),
            ],
            [
                'name' => 'contribution',
                'label' => $this->view->translate('Participants data'),
                'href' => $this->view->url(
                    ['controller' => 'consultation', 'action' => 'contribution-submission-form', 'kid' => $kid]
                ),
            ],
            [
                'name' => 'voting',
                'label' => $this->view->translate('Voting'),
                'href' => $this->view->url(['controller' => 'voting', 'action' => 'settings', 'kid' => $kid]),
            ],
            [
                'name' => 'phases',
                'label' => $this->view->translate('Phases'),
                'href' => $this->view->url(['controller' => 'consultation', 'action' => 'phases', 'kid' => $kid]),
            ],
        ];

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
