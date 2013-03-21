<?php
/**
 * Question Navigation
 *
 * @desc Navigation 3rd Level: Questions of a consultation
 * used in controllers: question, input, voting
 * @author Markus Hackel
 */
class Zend_View_Helper_QuestionNavigation extends Zend_View_Helper_Abstract {

  public function questionNavigation ($activeItem = null) {
    $con = $this->view->consultation;
    $questionModel = new Model_Questions();
    $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
    
    switch ($controllerName) {
      case 'voting':
        $urlParams = array();
        break;
      case 'question':
      case 'input':
      default:
        $urlParams = array(
          'action' => 'show',
          'page' => null,
        );
        break;
    }
    
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
      
      $urlParams['qid'] = $item->qi;
      $html.= '<a href="'
        . $this->view->url($urlParams) . '">'
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