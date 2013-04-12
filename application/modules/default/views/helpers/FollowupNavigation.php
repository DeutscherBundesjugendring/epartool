<?php

/**
 * Description of FollowupNavigation
 *
 * @author Marco Dinnbier
 */
class Zend_View_Helper_FollowupNavigation extends Zend_View_Helper_Abstract {
    
     public function followupNavigation ($activeItem = null, $activeTtemQ = null) {
         
        $html = '';
        $con = $this->view->consultation;
        
        
        if (!empty($con)) {
             
            $navItems = array(
                
              'overview' => array('title'=>'Übersicht Reaktionen & Wirkungen','url'=>array('action' => 'index', 'page' => null)),
              'byQuestion' => array('title'=>'Beiträge sortiert nach Fragen','url'=>array('action' => 'show', 'page' => null)),
              'byKeywords' => array('title'=>'Beiträge sortiert nach Schlagwörtern','url'=>array('action' => 'show', 'page' => null)),
        
                
            );
            
             $html = '<nav role="navigation" class="tertiary-navigation">'
                     . '<ul class="nav nav-list">';
             
             foreach ($navItems as $key => $val) { 
                $liClasses = array();
                if ($key == $activeItem) {
                    $liClasses[] = 'active';
                }
                $html.= '<li class="' . implode(' ', $liClasses) . '">';
                $html.= '<a href="'.$this->view->url($val['url']).'">';
                $html.= $val['title'];
                $html.= '</a>';                
                if($key == 'byQuestion') {
                     $this->view->QuestionNavigation($activeTtemQ);
                }
                $html.= '</li>';            
                 
             }
             

             
             $html.= '</ul></nav>';
             return $html; 
            
        }
         
     }
}

?>
