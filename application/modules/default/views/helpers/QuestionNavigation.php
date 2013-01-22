<?php
/**
 * Article Navigation
 *
 * @desc Navigation der 3. Ebene: Artikel/Infoseiten zu einer Konsultation
 * @author Markus Hackel
 */
class Zend_View_Helper_QuestionNavigation extends Zend_View_Helper_Abstract {
  
  public function questionNavigation ($activeItem = null) {
    $con = $this->view->consultation;
    $questionModel = new Model_Questions();
    $items = $questionModel->getByConsultation($con->kid);
    $html = '<nav role="navigation" class="tertiary-navigation">'
      . '<ul class="nav nav-list">';
    $i = 1;
    foreach ($items as $item) {
      $liClasses = array();
      if ($item->qi == $activeItem/* || (empty($activeItem) && $i == 1)*/) {
        $liClasses[] = 'active';
      }
      $html.= '<li class="' . implode(' ', $liClasses) . '">';
      $html.= '<a href="'
        . $this->view->url(array('action' => 'show', 'qid' => $item->qi, 'page' => null)) . '">'
        // Frage als Seitentitel im Menü
        . (empty($item->q) ? 'Frage ' . $i : $item->q)
        . '</a>';
      $html.= '</li>';
      $i++;
    }
    $html.= '</ul></nav>';
    return $html;
  }
}
?>