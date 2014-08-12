<?php

class Admin_View_Helper_SecondaryNavigation extends Zend_View_Helper_Abstract
{
    /**
     * Returns the navigation html
     * @param  array $items       An array holding the navigation items
     *                            @see self::getHtml() docblock for array structure
     * @param  string $activeItem The name of the active string
     * @return string             The navigation html
     */
    public function secondaryNavigation($items, $activeItem = null)
    {
        $html = '<div class="list-group list-group-nested">';
        foreach ($items as $item) {
            $current_user = Zend_Auth::getInstance()->getIdentity();
            if (!array_key_exists('required_user_level', $item) || $current_user->lvl === $item['required_user_level']) {
                $this->getItemHtml($item, $html, $activeItem);
            }
        }
        $html .= "</div>";

        return $html;
    }

    /**
     * Returns the html for a prticular navigation item
     * @param  array   $item       The item array in format
     *                             [
     *                                 'name' => Used to match with $activeItem
     *                                 'label' => label to be displayed
     *                                 'href' => menu link url
     *                                 'new_item' => new item link url
     *                                 'children' => array of child items
     *                             ]
     * @param  string  $html       The html produced so far
     * @param  string  $activeItem The name of the active item
     * @param  boolean $isNested   Indicates if it is a parent or nested item
     */
    private function getItemHtml(&$item, &$html, $activeItem = null, $isNested = null)
    {
        $childrenHtml = '';
        if (isset($item['children'])) {
            foreach ($item['children'] as $child) {
                $this->getItemHtml($child, $childrenHtml, $activeItem, true);
                $item['active'] = !empty($item['active']) ? true : $child['active'];
            }
        }

        $item['active'] = !empty($item['active']) ? true : ($item['name'] === $activeItem);
        if ($isNested) {
            $html .= '<a href="' . $item['href'] . '" class="list-group-item list-group-item-nested' . ($item['active'] ? ' active' : '' ) . '">';
            $html .= $this->view->translate($item['label']);
            $html .= "</a>";
        } else {
            $html .= '<a href="' . $item['href'] . '" class="list-group-item' . ($item['active'] ? ' active' : '' ) . '">';
            $html .= '<h4 class="list-group-item-heading">' . $this->view->translate($item['label']) . '</h4>';
            $html .= "</a>";
        }

        if (isset($item['new_item'])) {
            $html .= '<a href="' . $item['new_item'] . '" class="list-group-add" title="' . $this->view->translate('Create new item') . '">';
            $html .= '<span class="glyphicon glyphicon-plus-sign"></span>';
            $html .= '</a>';
        }

        $html .= $childrenHtml;
    }
}
