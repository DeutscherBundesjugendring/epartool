<?php
/**
 * Consultation Tabs
 */

class Admin_View_Helper_ConsultationTabs extends Zend_View_Helper_Abstract
{
    public function consultationTabs($id, $activeItem = null)
    {
        $view = Zend_Layout::getMvcInstance()->getView();

        $html = '<ul class="nav nav-tabs nav-tabs-header">' . "\n";

        $html .= '<li' . ($activeItem === 'consultation' ? ' class="active"' : '') . '>';
        $html .= '<a href="' . $view->url(array('controller' => 'consultation', 'action' => 'index', $id)) . '">';
        $html .= '<span class="glyphicon glyphicon-folder-close"></span> Consultation';
        $html .= '</a>';
        $html .= "</li>\n";

        $html .= '<li' . ($activeItem === 'settings' ? ' class="active"' : '') . '>';
        $html .= '<a href="' . $view->url(array('controller' => 'consultation', 'action' => 'edit', $id)) . '">';
        $html .= '<span class="glyphicon glyphicon-cog"></span> Settings';
        $html .= '</a>';
        $html .= "</li>\n";

        $html .= "</ul>\n";

        return $html;
    }
}
