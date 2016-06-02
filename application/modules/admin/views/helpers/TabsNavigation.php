<?php

class Admin_View_Helper_TabsNavigation extends Zend_View_Helper_Abstract
{
    /**
     * Returns the tab menu html
     * @param  array $items       The items of the menu inf format
     *                            [
     *                                'name' => Used to match with $activeItem
     *                                'label' => label to be displayed
     *                                'icon' => icon markup, optional
     *                                'href' => menu link url
     *                            ]
     * @param  string $activeItem The name of the active item
     * @return string             The menu html
     */
    public function tabsNavigation($items, $activeItem = null)
    {
        $html = '<ul role="tablist" class="nav nav-tabs nav-tabs-header">';

        foreach ($items as $item) {

            $html .= '<li' . ($activeItem === $item['name'] ? ' class="active"' : '') . '>';
            $html .= '<a href="' . $item['href'] . '">';
            $html .= isset($item['icon']) ? ($item['icon'] . ' ') : '';
            $html .= $item['label'];
            $html .= '</a>';
            $html .= "</li>";
        }

        $html .= "</ul>";

        return $html;
    }
}
