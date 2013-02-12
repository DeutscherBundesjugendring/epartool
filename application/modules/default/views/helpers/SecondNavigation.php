<?php
/**
 * Second Navigation
 *
 * @desc Navigation der 2. Ebene (Hauptaspekte einer Konsultation)
 * @author Markus Hackel
 */
class Zend_View_Helper_SecondNavigation extends Zend_View_Helper_Abstract {
  
  public function secondNavigation ($activeItem = null) {
    $date = new Zend_Date();
    $nowDate = Zend_Date::now();
    $con = $this->view->consultation;
    $disabled = array(
      'article' => false,
      'question' => false,
      'input' => ($nowDate->isEarlier($con->inp_fr) || $nowDate->isLater($con->inp_to)),
      'voting' => ($nowDate->isEarlier($con->vot_fr) || $nowDate->isLater($con->vot_to)),
      'follow-up' => (!$nowDate->isLater($con->vot_to)),
    );
    $items = array(
      'article' => array('url' => '/article/index/kid/' . $con->kid, 'text' => 'Infos'),
      'question' => array('url' => '/question/index/kid/' . $con->kid, 'text' => 'Fragen'),
      'input' => array(
        'url' => '/input/index/kid/' . $con->kid,
        'text' => 'Beiträge <small class="info">vom '
          . $date->set($con->inp_fr)->get(Zend_Date::DATE_MEDIUM, new Zend_Locale()) . '</small>'
      ),
      'voting' => array(
        'url' => '/voting/index/kid/' . $con->kid,
        'text' => 'Abstimmung <small class="info">vom '
          . $date->set($con->vot_fr)->get(Zend_Date::DATE_MEDIUM, new Zend_Locale()) . '</small>'
      ),
      'follow-up' => array(
        'url' => '',
        'text' => 'Reaktionen & Wirkung <small class="info">nach Ende der Abstimmung</small>'
      ),
    );
    $html = '<nav role="navigation" class="secondary-navigation">'
      . '<ul class="nav nav-tabs">';
    foreach ($items as $item => $val) {
      $liClasses = array();
      if ($item == $activeItem) {
        $liClasses[] = 'active';
      }
      if ($disabled[$item]) {
        $liClasses[] = 'disabled';
      }
      $html.= '<li class="' . implode(' ', $liClasses) . '">';
      if (!empty($val['url']) && !in_array('disabled', $liClasses)) {
        $html.= '<a href="' . $val['url'] . '">' . $val['text'] . '</a>';
      } else {
        $html.= '<div>' . $val['text'] . '</div>';
      }
      $html.= '</li>';
    }
    $html.= '</ul></nav>';
    return $html;
  }
}
?>