<?php
/**
 * View Helper für die Login Box
 * @author Markus
 *
 */
class Zend_View_Helper_Login extends Zend_View_Helper_Abstract {
  public function login() {
    $html = '';
    $auth = Zend_Auth::getInstance();
    if (!$auth->hasIdentity()) {
      // not logged in, show login button and form
      $html = '<a href="#login" class="btn btn-block" role="button" data-toggle="modal">Login</a>';
      $form = new Default_Form_Login();
      $html.= '<!-- Login form in a modified modal -->'
        . '<div id="login" class="login modal hide fade" tabindex="-1" role="dialog" aria-labelledby="loginLabel" aria-hidden="true">'
        . '  <div class="modal-header">'
        . '    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'
        . '    <h3 id="loginLabel">Login</h3>'
        . '  </div><!-- .modal-header -->'
        . '<div class="modal-body">'
        . $form
        . '<p><a href="'
        . $this->view->url(array('controller' => 'user', 'action' => 'passwordrecover'), 'default', true)
        . '">Passwort vergessen?</a></p>'
        . '</div><!-- .modal-body -->'
        . '</div><!-- #login -->';
    }
    return $html;
  }
}