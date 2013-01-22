<?php
/**
 * Article Navigation
 *
 * @desc Navigation der 3. Ebene: Artikel/Infoseiten zu einer Konsultation
 * @author Markus Hackel
 */
class Zend_View_Helper_ArticleNavigation extends Zend_View_Helper_Abstract {
  
  public function articleNavigation ($activeItem = null) {
    $con = $this->view->consultation;
    $articleModel = new Model_Articles();
    $items = $articleModel->getByConsultation($con->kid);
    $html = '<nav role="navigation" class="tertiary-navigation">'
      . '<ul class="nav nav-list">';
    $i = 1;
    foreach ($items as $item) {
      if ($item->hid == 'n') {
        $liClasses = array();
        if ($item->art_id == $activeItem/* || (empty($activeItem) && $i == 1)*/) {
          $liClasses[] = 'active';
        }
        $html.= '<li class="' . implode(' ', $liClasses) . '">';
        $html.= '<a href="'
          . $this->view->url(array('action' => 'show', 'aid' => $item->art_id)) . '">'
          // desc als Seitentitel im Menü
          . (empty($item->desc) ? 'Seite ' . $i : $item->desc)
          . '</a>';
        $html.= '</li>';
        $i++;
      }
    }
    $html.= '</ul></nav>';
    return $html;
  }
}
?>