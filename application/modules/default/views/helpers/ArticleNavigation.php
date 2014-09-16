<?php
/**
 * Article Navigation
 *
 * @desc Navigation der 3. Ebene: Artikel/Infoseiten zu einer Konsultation
 * @author Markus Hackel
 */
class Zend_View_Helper_ArticleNavigation extends Zend_View_Helper_Abstract
{
    public function articleNavigation($activeItem = null, $scope = 'info')
    {
        $html = '';
        $con = $this->view->consultation;
        $articleModel = new Model_Articles();
        if (!empty($con)) {
            // consultation is set -> show appropriate articles/pages
            $items = $articleModel->getByConsultation($con->kid, $scope);
        } else {
            $scope = 'static';
            $items = $articleModel->getStaticPages();
        }
            $html = '<nav role="navigation" class="tertiary-navigation">'
                . '<ul class="nav nav-list">';
            $i = 1;
            foreach ($items as $item) {
                if ($item['ref_nm'] == 'about') {
                    // "about" page should not be visible in this menu
                    continue;
                }
                switch ($scope) {
                    case 'static':
                        $route = $item['ref_nm'];
                        break;

                    default:
                        $route = 'default';
                }
                $isItemInRootline = false;
                // first level
                if ($item['hid'] == 'n') {
                    // show only unhidden pages
                    $liClasses = array();
                    // is item active itself OR is in rootline (i.e. one of its subpages is active)?
                    $isItemInRootline = ($item['art_id'] == $activeItem
                        || (!empty($item['subpages']) && array_key_exists($activeItem, $item['subpages'])));
                    if ($isItemInRootline) {
                        $liClasses[] = 'active';
                    }
                    $html.= '<li class="' . implode(' ', $liClasses) . '">';
                    $html.= '<a href="'
                        . $this->view->url(array(
                                'controller' => 'article',
                                'action' => 'show',
                                'aid' => $item['art_id']), $route, null) . '">'
                        // desc als Seitentitel im Menü
                        . (empty($item['desc']) ? $this->view->translate('Page'). ' ' . $i : $item['desc'])
                        . '</a>';
                    if (!empty($item['subpages']) && $isItemInRootline) {
                        $html.= '<ul class="nav nav-list">';
                        $j = 1;
                        foreach ($item['subpages'] as $subpage) {
                            // second level (subpages)
                            if ($subpage['hid'] == 'n') {
                                // show only unhidden pages
                                $liClassesSub = array();
                                if ($subpage['art_id'] == $activeItem) {
                                    $liClassesSub[] = 'active';
                                }
                                $html.= '<li class="' . implode(' ', $liClassesSub) . '">';
                                $html.= '<a href="'
                                    . $this->view->url(array(
                                            'controller' => 'article',
                                            'action' => 'show',
                                            'aid' => $subpage['art_id']), 'default', null) . '">'
                                    // desc als Seitentitel im Menü
                                    . (empty($subpage['desc']) ? $this->view->translate('Page') . ' ' . $i . '.' . $j : $subpage['desc'])
                                    . '</a>';
                                $html.= '</li>';
                                $j++;
                            }
                        }
                        $html.= '</ul>';
                    }
                    $html.= '</li>';
                    $i++;
                }
            }
            $html.= '</ul></nav>';

        return $html;
    }
}
