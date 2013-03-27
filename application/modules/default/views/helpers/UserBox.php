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
        . 'Eingeloggt als <strong>' . $identity->name . '</strong> mit '
        . '<a href="mailto:' . $identity->email . '">' . $identity->email . '</a>'
        . '</div>'
        . '<div class="dropdown pull-right">'
        . '<a href="#" role="button" class="btn btn-icon dropdown-toggle" id="userDropdown" data-toggle="dropdown"><i class="icon-angle-down"></i></a>'
        . '<ul class="dropdown-menu" role="menu" aria-labelledby="userDropdown">'
        . '<li><a href="' . $this->view->url(array('controller' => 'user', 'action' => 'inputlist'), 'default', true) . '"><i class="icon-list"></i> Alle meine Beiträge ansehen</a></li>';
      if ($identity->lvl == 'adm' || $identity->lvl == 'edt') {
          $html.= '<li><a href="' . $this->view->baseUrl() . '/admin"><i class="icon-cog"></i> Zum Adminbereich</a></li>';
        }
      $html .= '<li><a href="' . $this->view->url(array('controller' => 'user', 'action' => 'logout'), 'default', true) . '"><i class="icon-signout"></i> Logout</a></li>'
        . '</ul>'
        . '</div>'
        . '</div>';
    }
    return $html;
  }
}