<?php
/**
 * Article Navigation
 *
 * @desc Navigation der 3. Ebene: Artikel/Infoseiten zu einer Konsultation
 * @author Markus Hackel
 */
class Zend_View_Helper_ArticleNavigation extends Zend_View_Helper_Abstract {
  
  public function articleNavigation ($activeItem = null) {
    $html = '';
    $con = $this->view->consultation;
    if (!empty($con)) {
      // consultation is set -> show appropriate articles/pages
      $articleModel = new Model_Articles();
      $items = $articleModel->getByConsultation($con->kid);
      $html = '<nav role="navigation" class="tertiary-navigation">'
        . '<ul class="nav nav-list">';
      $i = 1;
      foreach ($items as $item) {
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
            . $this->view->url(array('action' => 'show', 'aid' => $item['art_id'])) . '">'
            // desc als Seitentitel im Menü
            . (empty($item['desc']) ? 'Seite ' . $i : $item['desc'])
            . '</a>';
          if (!empty($item['subpages']) && $isItemInRootline) {
            $html.= '<ul>';
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
                  . $this->view->url(array('action' => 'show', 'aid' => $subpage['art_id'])) . '">'
                  // desc als Seitentitel im Menü
                  . (empty($subpage['desc']) ? 'Seite ' . $i . '.' . $j : $subpage['desc'])
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
    } else {
      // no consultation set -> show general/static pages
      $currentUrl = $this->view->url();
      $html = '<nav role="navigation" class="tertiary-navigation">'
        . '<ul class="nav nav-list">';
      $html.= '<li' . (stristr($currentUrl, 'imprint') ? ' class="active"' : '')
        . '><a href="' . $this->view->url(array(), 'imprint', true) . '">Impressum</a></li>';
      $html.= '<li' . (stristr($currentUrl, 'about') ? ' class="active"' : '')
        . '><a href="' . $this->view->url(array(), 'about', true) . '">Über uns</a></li>';
      $html.= '<li' . (stristr($currentUrl, 'faq') ? ' class="active"' : '')
        . '><a href="' . $this->view->url(array(), 'faq', true) . '">Häufige Fragen</a></li>';
      $html.= '<li' . (stristr($currentUrl, 'privacy') ? ' class="active"' : '')
        . '><a href="' . $this->view->url(array(), 'privacy', true) . '">Datenschutz</a></li>';
      $html.= '<li' . (stristr($currentUrl, 'contact') ? ' class="active"' : '')
        . '><a href="' . $this->view->url(array(), 'contact', true) . '">Kontakt</a></li>';
      $html.= '</ul></nav>';
    }
    return $html;
  }
}
?>