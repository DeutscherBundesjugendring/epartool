<?php
/**
 * View Helper für die User Box
 * @author Markus
 *
 */
class Zend_View_Helper_UserBox extends Zend_View_Helper_Abstract
{
    public function userBox()
    {
        $html = '';
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $html = '<div class="user-box hidden-print">'
                . '<div class="user pull-left">'
                . sprintf($this->view->translate('Logged in as %s with'), '<strong>' . $identity->name . '</strong>')
                . ' <a href="mailto:' . $identity->email . '">' . $identity->email . '</a>'
                . '</div>'
                . '<div class="dropdown pull-right">'
                . '<a href="#" role="button" class="btn btn-icon dropdown-toggle" id="userDropdown" data-toggle="dropdown"><i class="icon-angle-down"></i></a>'
                . '<ul class="dropdown-menu" role="menu" aria-labelledby="userDropdown">'
                . '<li><a href="' . $this->view->url(array('controller' => 'user', 'action' => 'inputlist'), 'default', true) . '"><i class="icon-list"></i> ' . $this->view->translate('View all my contributions') . '</a></li>'
				. '<li><a href="' . $this->view->url(array('controller' => 'user', 'action' => 'userlist'), 'default', true) . '"><i class="icon-list"></i> ' . $this->view->translate('View group members') . '</a></li>';
            if ($identity->lvl == 'adm' || $identity->lvl == 'edt') {
                    $html.= '<li><a href="' . $this->view->baseUrl() . '/admin"><i class="icon-cog"></i> ' . $this->view->translate('To admin pages') . '</a></li>';
                }
            $html .= '<li><a href="' . $this->view->url(array('controller' => 'user', 'action' => 'logout'), 'default', true) . '"><i class="icon-signout"></i> ' . $this->view->translate('Logout') . '</a></li>'
                . '</ul>'
                . '</div>'
                . '</div>';
        }

        return $html;
    }
}
