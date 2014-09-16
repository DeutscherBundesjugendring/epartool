<?php
/**
 * Consultation Navigation
 * Navigation der 3. Ebene: Artikel/Infoseiten zu einer Konsultation
 *
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
                    'label' => 'Grundeinstellungen',
                    'href' => $this->view->baseUrl() . '/admin/consultation/edit/kid/' . $kid
                ),
                array(
                    'label' => 'Abstimmung vorbereiten',
                    'href' => $this->view->baseUrl() . '/admin/votingprepare/index/kid/' . $kid
                ),
                array(
                    'label' => 'Mediathek',
                    'href' => $this->view->baseUrl() . '/admin/media/index/kid/' . $kid
                ),
                array(
                    'label' => 'Fragen',
                    'href' => $this->view->baseUrl() . '/admin/question/index/kid/' . $kid
                ),
                array(
                    'label' => 'Artikel',
                    'href' => $this->view->baseUrl() . '/admin/article/index/kid/' . $kid
                ),
                array(
                    'label' => 'Beiträge',
                    'href' => $this->view->baseUrl() . '/admin/input/index/kid/' . $kid
                ),
                 array(
                    'label' => 'Abstimmungseinstellungen',
                    'href' => $this->view->baseUrl() . '/admin/voting/settings/kid/' . $kid
                ),
                array(
                    'label' => 'Abstimmungsberechtigungen',
                    'href' => $this->view->baseUrl() . '/admin/voting/index/kid/' . $kid
                ),
                array(
                    'label' => 'Abstimmungseinladungen',
                    'href' => $this->view->baseUrl() . '/admin/voting/invitations/kid/' . $kid
                ),
                array(
                    'label' => 'Abstimmungsteilnehmende',
                    'href' => $this->view->baseUrl() . '/admin/voting/participants/kid/' . $kid
                ),
                array(
                    'label' => 'Abstimmungsergebnisse',
                    'href' => $this->view->baseUrl() . '/admin/voting/results/kid/' . $kid
                ),
                array(
                    'label' => 'Reaktionen & Wirkung',
                    'href' => $this->view->baseUrl() . '/admin/followup/index/kid/' . $kid
                ),
                /*
                 array(
                    'label' => 'Ordnerverwaltung',
                    'href' => $this->view->baseUrl() . '/admin/directories/index/kid/' . $kid
                ),
                */
                array(
                    'label' => 'Statistik',
                    'href' => $this->view->baseUrl() . '/admin/consultation/report/kid/' . $kid
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
            $html.= '<div class="hlist"><ul>';
            foreach ($items as $item) {
            // JSU check if required userlevel is given, otherwise dont show this entry
                $current_user = Zend_Auth::getInstance()->getIdentity();
                if (!array_key_exists('required_userlevel', $item) || $current_user->lvl === $item['required_userlevel']) {
                    // JSU add class-option to html
                    $additionalClass = (array_key_exists('class', $item)) ? $item['class'] : '' ;
                    if ($item['label'] == $activeItem) {
    //                    $html.= '<li class="active"><strong>' . $item['label'] . '</strong></li>';
                        $html.= '<li class="active '.$additionalClass.'"><a href="' . $item['href'] . '">' . $item['label'] . '</a></li>';
                    } else {
                        $html.= '<li class="'.$additionalClass.'"><a href="' . $item['href'] . '">' . $item['label'] . '</a></li>';
                    }
                }
            }
            $html.= '</ul></div>';
        }

        return $html;
    }
}
