<?php
/**
 * Email Tabs
 *
 * TODO refactor to single tabs helper
 */

class Admin_View_Helper_EmailTabs extends Zend_View_Helper_Abstract
{
    public function emailTabs($activeItem = null)
    {
        $view = Zend_Layout::getMvcInstance()->getView();

        $html = '<ul class="nav nav-tabs nav-tabs-header">' . "\n";

        $html .= '<li' . ($activeItem === 'emailing' ? ' class="active"' : '') . '>';
        $html .= '<a href="' . $view->url(array('controller' => 'mail-sent', 'action' => 'index')) . '">';
        $html .= '<span class="glyphicon glyphicon-envelope "></span> Emailing';
        $html .= '</a>';
        $html .= "</li>\n";

        $html .= '<li' . ($activeItem === 'settings' ? ' class="active"' : '') . '>';
        $html .= '<a href="' . $view->url(array('controller' => 'mail-component', 'action' => 'index')) . '">';
        $html .= '<span class="glyphicon glyphicon-cog"></span> Settings';
        $html .= '</a>';
        $html .= "</li>\n";

        $html .= "</ul>\n";

        return $html;
    }
}
