<?php
/**
 * Consultation Navigation
 */

class Admin_View_Helper_ConsultationNavigation extends Zend_View_Helper_Abstract
{
    public function consultationNavigation($activeItem = null)
    {
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        $kid = $request->getParam('kid', 0);
        $html = '';

        // TODO move to ini file?
        if ($kid > 0) {
            $items = array(
                array(
                    'name' => 'dashboard',
                    'label' => 'Dashboard',
                    'href' => $this->view->baseUrl() . '/admin/consultation/index/kid/' . $kid,
                ),
                array(
                    'name' => 'info',
                    'label' => 'Info',
                    'href' => $this->view->baseUrl() . '/admin/article/index/kid/' . $kid,
                ),
                array(
                    'name' => 'questions',
                    'label' => 'Questions',
                    'href' => $this->view->baseUrl() . '/admin/question/index/kid/' . $kid,
                ),
                array(
                    'name' => 'contributions',
                    'label' => 'Contributions',
                    'href' => $this->view->baseUrl() . '/admin/input/index/kid/' . $kid,
                ),
                /*array(
                    'name' => 'media',
                    'label' => 'Media',
                    'href' => $this->view->baseUrl() . '/admin/media/index/kid/' . $kid,
                ),*/
                array(
                    'name' => 'voting-prepare',
                    'label' => 'Voting',
                    'href' => $this->view->baseUrl() . '/admin/votingprepare/index/kid/' . $kid,
                ),
                array(
                    'name' => 'voting-settings',
                    'label' => 'Settings',
                    'href' => $this->view->baseUrl() . '/admin/voting/settings/kid/' . $kid,
                    'level' => 2,
                ),
                array(
                    'name' => 'voting-permissions',
                    'label' => 'Permissions',
                    'href' => $this->view->baseUrl() . '/admin/voting/index/kid/' . $kid,
                    'level' => 2,
                ),
                array(
                    'name' => 'voting-invitations',
                    'label' => 'Invitations',
                    'href' => $this->view->baseUrl() . '/admin/voting/invitations/kid/' . $kid,
                    'level' => 2,
                ),
                array(
                    'name' => 'voting-participants',
                    'label' => 'Participants',
                    'href' => $this->view->baseUrl() . '/admin/voting/participants/kid/' . $kid,
                    'level' => 2,
                ),
                array(
                    'name' => 'voting-results',
                    'label' => 'Results',
                    'href' => $this->view->baseUrl() . '/admin/voting/results/kid/' . $kid,
                    'level' => 2,
                ),
                array(
                    'name' => 'followup',
                    'label' => 'Follow Up',
                    'href' => $this->view->baseUrl() . '/admin/followup/index/kid/' . $kid,
                ),
                array(
                    'name' => 'stats',
                    'label' => 'Statistics',
                    'href' => $this->view->baseUrl() . '/admin/consultation/report/kid/' . $kid,
                ),
            );

            $html .= '<div class="list-group list-group-nested">' . "\n";

            foreach ($items as $item) {
                $current_user = Zend_Auth::getInstance()->getIdentity();
                if (!array_key_exists('required_userlevel', $item) || $current_user->lvl === $item['required_userlevel']) {
                    if (isset($item['level']) && $item['level'] === 2) {
                        $html .= '<a href="' . $item['href'] . '" class="list-group-item list-group-item-nested' . ($item['name'] === $activeItem ? ' active' : '' ) . '">';
                        $html .= $item['label'];
                        $html .= "</a>\n";
                    } else {
                        $html .= '<a href="' . $item['href'] . '" class="list-group-item' . ($item['name'] === $activeItem ? ' active' : '' ) . '">';
                        $html .= '<h4 class="list-group-item-heading">' . $item['label'] . '</h4>';
                        $html .= "</a>\n";
                    }
                }
            }

            $html .= "</div>\n";
        }

        return $html;
    }
}
