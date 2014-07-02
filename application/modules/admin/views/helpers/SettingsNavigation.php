<?php
/**
 * Settings Navigation
 *
 * TODO refactor and merge with consultation navigation
 */

class Admin_View_Helper_SettingsNavigation extends Zend_View_Helper_Abstract
{
    public function settingsNavigation($activeItem = null)
    {
        $html = '';

        $items = array(
            array(
                'name' => 'site',
                'label' => 'Site',
                'href' => $this->view->url(array('controller' => 'settings', 'action' => 'index')),
            ),
            array(
                'name' => 'pages',
                'label' => 'Pages',
                'href' => $this->view->url(array('controller' => 'article', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'article', 'action' => 'create')),
            ),
            array(
                'name' => 'keywords',
                'label' => 'Keywords',
                'href' => $this->view->url(array('controller' => 'tag', 'action' => 'index')),
                'new_item' => $this->view->url(array('controller' => 'tag', 'action' => 'create')),
            ),
            // TODO DBJR-89
            /*array(
                'name' => 'header',
                'label' => 'Header',
                'href' => $this->view->url(array('controller' => 'settings', 'action' => 'index')),
            ),
            array(
                'name' => 'footer',
                'label' => 'Footer',
                'href' => $this->view->url(array('controller' => 'settings', 'action' => 'index')),
            ),*/
        );

        $html .= '<div class="list-group list-group-nested">' . "\n";

        foreach ($items as $item) {
            $current_user = Zend_Auth::getInstance()->getIdentity();
            if (!array_key_exists('required_user_level', $item) || $current_user->lvl === $item['required_user_level']) {
                if (isset($item['level']) && $item['level'] === 2) {
                    $html .= '<a href="' . $item['href'] . '" class="list-group-item list-group-item-nested' . ($item['name'] === $activeItem ? ' active' : '' ) . '">';
                    $html .= $item['label'];
                    $html .= "</a>\n";
                } else {
                    $html .= '<a href="' . $item['href'] . '" class="list-group-item' . ($item['name'] === $activeItem ? ' active' : '' ) . '">';
                    $html .= '<h4 class="list-group-item-heading">' . $item['label'] . '</h4>';
                    $html .= "</a>\n";
                }
                if (isset($item['new_item'])) {
                    $html .= '<a href="' . $item['new_item'] . '" class="list-group-add" title="Create new item"><span class="glyphicon glyphicon-plus-sign"></span></a>' . "\n";
                }
            }
        }

        $html .= "</div>\n";

        return $html;
    }
}
