<?php
/**
 * View Helper für die Login Box
 * @author Markus
 *
 */
class Zend_View_Helper_Login extends Zend_View_Helper_Abstract
{
    public function login()
    {
        $html = '';
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $html = '<div class="dropdown hidden-print">'
                . '<a href="#" role="button" class="btn btn-block" id="loginDropdown" data-toggle="dropdown">Login</a>';
            $form = new Default_Form_Login();
            $html.= '<div class="login dropdown-menu pull-right" role="menu" aria-labelledby="loginDropdown">'
                . '        <h3 id="loginLabel">' . $this->view->translate('Login') . '</h3>'
                . $form
                . '<hr />'
                . '<p><a href="'
                . $this->view->url(array('controller' => 'user', 'action' => 'passwordrecover'), 'default', true)
                . '">' . $this->view->translate('Forgot password?') . '</a></p>'
                . '</div>'
                . '</div><!-- .dropdown -->';
        }

        return $html;
    }
}
