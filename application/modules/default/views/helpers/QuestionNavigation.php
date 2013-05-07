<?php
/**
 * Article Navigation
 *
 * @desc Navigation der 3. Ebene: Artikel/Infoseiten zu einer Konsultation
 * @author Markus Hackel
 */
class Zend_View_Helper_QuestionNavigation extends Zend_View_Helper_Abstract {

  public function questionNavigation ($activeItem = null,$for = NULL, $numbered = false) {
    $con = $this->view->consultation;
    $questionModel = new Model_Questions();
    $items = $questionModel->getByConsultation($con->kid);
    
    $navclass = $for == 'follow-up' ? 'level4-navigation' : 'tertiary-navigation';
    $action = $for == 'follow-up' ? 'by-question' : 'show';
    
    $html = '<nav role="navigation" class="'.$navclass.'">';
    $html.= '<ul class="nav nav-list">';
    $i = 1;
    foreach ($items as $item) {
      $number = $numbered ? $i.'. ' : '';
      $liClasses = array();
      if ($item->qi == $activeItem/* || (empty($activeItem) && $i == 1)*/) {
        $liClasses[] = 'active';
      }
      $html.= '<li class="' . implode(' ', $liClasses) . '">';
      $html.= '<a href="'
        . $this->view->url(array('action' => $action, 'qid' => $item->qi, 'page' => null)) . '">'
        // Frage als Seitentitel im Menü
        . (empty($item->q) ? 'Frage ' . $i : $number.$item->q)
        . '</a>';
      $html.= '</li>';
      $i++;
    }
    $html.= '</ul></nav>';
    
    return $html;
  }
}
?>