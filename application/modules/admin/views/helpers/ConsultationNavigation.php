<?php

class Admin_View_Helper_ConsultationNavigation extends Zend_View_Helper_Abstract
{
    /**
     * Returns consultation secondary navigation html
     * @param  integer $kid         Identifier of the consultation
     * @param  string  $activeItem  Name of the active item
     * @return string               The menu html
     */
    public function consultationNavigation($kid, $activeItem = null)
    {
        $items = array(
            array(
                'name' => 'dashboard',
                'label' => 'Dashboard',
                'href' => $this->view->url(array('controller' => 'consultation', 'action' => 'index', 'kid' => $kid)),
            ),
            array(
                'name' => 'info',
                'label' => 'Info',
                'href' => $this->view->url(array('controller' => 'article', 'action' => 'index', 'kid' => $kid)),
                'new_item' => $this->view->url(array('controller' => 'article', 'action' => 'create', 'kid' => $kid)),
            ),
            array(
                'name' => 'questions',
                'label' => 'Questions',
                'href' => $this->view->url(array('controller' => 'question', 'action' => 'index', 'kid' => $kid)),
                'new_item' => $this->view->url(array('controller' => 'question', 'action' => 'create', 'kid' => $kid)),
            ),
            array(
                'name' => 'contributions',
                'label' => 'Contributions',
                'href' => $this->view->url(array('controller' => 'input', 'action' => 'index', 'kid' => $kid)),
            ),
            array(
                'name' => 'voting-prepare',
                'label' => 'Voting',
                'href' => $this->view->url(array('controller' => 'votingprepare', 'action' => 'index', 'kid' => $kid)),
                'children' => [
                    array(
                        'name' => 'voting-permissions',
                        'label' => 'Permissions',
                        'href' => $this->view->url(array('controller' => 'voting', 'action' => 'index', 'kid' => $kid)),
                    ),
                    array(
                        'name' => 'voting-invitations',
                        'label' => 'Invitations',
                        'href' => $this->view->url(array('controller' => 'voting', 'action' => 'invitations', 'kid' => $kid)),
                    ),
                    array(
                        'name' => 'voting-participants',
                        'label' => 'Participants',
                        'href' => $this->view->url(array('controller' => 'voting', 'action' => 'participants', 'kid' => $kid)),
                    ),
                    array(
                        'name' => 'voting-results',
                        'label' => 'Results',
                        'href' => $this->view->url(array('controller' => 'voting', 'action' => 'results', 'kid' => $kid)),
                    ),
                ]
            ),
            array(
                'name' => 'followup',
                'label' => 'Follow Up',
                'href' => $this->view->url(array('controller' => 'followup', 'action' => 'index', 'kid' => $kid)),
                'new_item' => $this->view->url(array('controller' => 'followup', 'action' => 'create-followup', 'kid' => $kid)),
            ),
            array(
                'name' => 'stats',
                'label' => 'Statistics',
                'href' => $this->view->url(array('controller' => 'consultation', 'action' => 'report', 'kid' => $kid)),
            ),
        );

        return $this->view->secondaryNavigation($items, $activeItem);
    }
}
