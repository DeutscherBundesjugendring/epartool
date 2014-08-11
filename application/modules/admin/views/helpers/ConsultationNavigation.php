<?php
/**
 * Consultation Navigation
 *
 * TODO refactor and merge with settings navigation
 */

class Admin_View_Helper_ConsultationNavigation extends Zend_View_Helper_Abstract
{
    public function consultationNavigation($activeItem = null)
    {
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        $kid = $request->getParam('kid', 0);
        $html = '';

        if ($kid > 0) {
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
                ),
                array(
                    'name' => 'voting-settings',
                    'label' => 'Settings',
                    'href' => $this->view->url(array('controller' => 'voting', 'action' => 'settings', 'kid' => $kid)),
                    'level' => 2,
                ),
                array(
                    'name' => 'voting-permissions',
                    'label' => 'Permissions',
                    'href' => $this->view->url(array('controller' => 'voting', 'action' => 'index', 'kid' => $kid)),
                    'level' => 2,
                ),
                array(
                    'name' => 'voting-invitations',
                    'label' => 'Invitations',
                    'href' => $this->view->url(array('controller' => 'voting', 'action' => 'invitations', 'kid' => $kid)),
                    'level' => 2,
                ),
                array(
                    'name' => 'voting-participants',
                    'label' => 'Participants',
                    'href' => $this->view->url(array('controller' => 'voting', 'action' => 'participants', 'kid' => $kid)),
                    'level' => 2,
                ),
                array(
                    'name' => 'voting-results',
                    'label' => 'Results',
                    'href' => $this->view->url(array('controller' => 'voting', 'action' => 'results', 'kid' => $kid)),
                    'level' => 2,
                ),
                array(
                    'name' => 'followup',
                    'label' => 'Follow Up',
                    'href' => $this->view->url(array('controller' => 'followup', 'action' => 'index', 'kid' => $kid)),
                    'new_item' => $this->view->url(array('controller' => 'followup', 'action' => 'create-file', 'kid' => $kid)),
                ),
                array(
                    'name' => 'stats',
                    'label' => 'Statistics',
                    'href' => $this->view->url(array('controller' => 'consultation', 'action' => 'report', 'kid' => $kid)),
                ),
                 array(
                    'label' => 'Beteiligungsrunde schließen',
                    'href' => $this->view->baseUrl() . '/admin/close/index/kid/' . $kid
                ),
                array(
                    'label' => 'Beteiligungsrunde löschen',
                    'href' => $this->view->baseUrl() . '/admin/consultation/delete/kid/' . $kid,
                    'class' => 'button_red delete-action',
                    'required_userlevel' => 'adm'
                ),
            );

            $html .= '<div class="list-group list-group-nested">' . "\n";

            foreach ($items as $item) {
                $current_user = Zend_Auth::getInstance()->getIdentity();
                if (!array_key_exists('required_user_level', $item) || $current_user->lvl === $item['required_user_level']) {
                    if (isset($item['level']) && $item['level'] === 2) {
                        $html .= '<a href="' . $item['href'] . '" class="list-group-item list-group-item-nested' . ($item['name'] === $activeItem ? ' active' : '' ) . '">';
                        $html .= $item['label'];
                        $html .= "</a>\n";
                    } else {
                        $html .= '<a href="' . $item['href'] . '" class="list-group-item' . ($item['name'] === $activeItem ? ' active' : '' ) . '">';
                        $html .= '<h4 class="list-group-item-heading">' . $item['label'] . '</h4>';
                        $html .= "</a>\n";
                    }
                    if (isset($item['new_item'])) {
                        $html .= '<a href="' . $item['new_item'] . '" class="list-group-add" title="Create new item"><span class="glyphicon glyphicon-plus-sign"></span></a>' . "\n";
                    }
                }
            }

            $html .= "</div>\n";
        }

        return $html;
    }
}
