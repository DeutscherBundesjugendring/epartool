<?php
/**
 * View Helper für die User Box
 * @author Markus
 *
 */
class Zend_View_Helper_UserBox extends Zend_View_Helper_Abstract {
  
  public function userBox() {
    $html = '';
    $auth = Zend_Auth::getInstance();
    if ($auth->hasIdentity()) {
      $identity = $auth->getIdentity();
      $html = '<div class="user-box">'
        . '<div class="user pull-left">'
        . 'Eingeloggt als ' . $identity->name . ' mit '
        . '<a href="mailto:' . $identity->email . '">' . $identity->email . '</a>';
        if ($identity->lvl == 'adm' || $identity->lvl == 'edt') {
          $html.= '<br/><a href="' . $this->view->baseUrl() . '/admin">Zum Adminbereich &raquo;</a>';
        }
      $html.= '</div>'
        . '<a href="#user-info" class="btn pull-right" role="button" data-toggle="modal"><i class="icon-chevron-down"></i></a>'
        . '</div>';
    }
    return $html;
  }
}