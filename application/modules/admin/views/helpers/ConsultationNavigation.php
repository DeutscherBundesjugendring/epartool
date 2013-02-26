<?php
/**
 * Consultation Navigation
 * Navigation der 3. Ebene: Artikel/Infoseiten zu einer Konsultation
 *
 */
class Admin_View_Helper_ConsultationNavigation extends Zend_View_Helper_Abstract {
  
  public function consultationNavigation ($activeItem = null) {
    $front = Zend_Controller_Front::getInstance();
    $request = $front->getRequest();
    $kid = $request->getParam('kid', 0);
    
    $html = '';
    if ($kid > 0) {
      $items = array(
        array(
          'label' => 'Grundeinstellungen',
          'href' => '/admin/consultation/edit/kid/' . $kid
        ),
        array(
          'label' => 'Medienverwaltung',
          'href' => '/admin/media/index/kid/' . $kid
        ),
        array(
          'label' => 'Fragen',
          'href' => '/admin/question/index/kid/' . $kid
        ),
        array(
          'label' => 'Artikel',
          'href' => '/admin/article/index/kid/' . $kid
        ),
        array(
          'label' => 'Beiträge',
          'href' => '/admin/input/index/kid/' . $kid
        ),
        array(
          'label' => 'Votingberechtigungen',
          'href' => '/admin/voting/index/kid/' . $kid
        ),
//        array(
//          'label' => 'Votingeinladungen',
//          'href' => '/admin/voting/invitations/kid/' . $kid
//        ),
//        array(
//          'label' => 'Votingteilnehmende',
//          'href' => '/admin/voting/participants/kid/' . $kid
//        ),
//        array(
//          'label' => 'Votingergebnisse',
//          'href' => '/admin/voting/results/kid/' . $kid
//        ),
//        array(
//          'label' => 'Statistik',
//          'href' => '/admin/consultation/report/kid/' . $kid
//        ),
      );
      $html.= '<div class="hlist"><ul>';
      foreach ($items as $item) {
        if ($item['label'] == $activeItem) {
//          $html.= '<li class="active"><strong>' . $item['label'] . '</strong></li>';
          $html.= '<li class="active"><a href="' . $item['href'] . '">' . $item['label'] . '</a></li>';
        } else {
          $html.= '<li><a href="' . $item['href'] . '">' . $item['label'] . '</a></li>';
        }
      }
      $html.= '</ul></div>';
    }
    return $html;
  }
}
?>