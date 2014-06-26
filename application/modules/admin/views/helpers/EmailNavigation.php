<?php
/**
 * Email Navigation
 *
 * TODO refactor and merge with consultation navigation
 */

class Admin_View_Helper_EmailNavigation extends Zend_View_Helper_Abstract
{
    public function emailNavigation($activeItem = null)
    {
        $html = '';

        $items = array(
            array(
                'name' => 'sent',
                'label' => 'Sent',
                'href' => $this->view->url(array('controller' => 'mail-sent', 'action' => 'index')),
            ),
            array(
                'name' => 'queued',
                'label' => 'Queued',
                'href' => $this->view->url(array('controller' => 'mail-queued', 'action' => 'index')),
            ),
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
