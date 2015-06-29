<?php

class Module_Default_View_Helper_FollowupNavigation extends Zend_View_Helper_Abstract
{

    public function followupNavigation($activeItem = null, $activeItemQ = null)
    {
        $html = '';
        $con = $this->view->consultation;

        if (!empty($con)) {
            $navItems = array(
                'overview' => array('title' => $this->view->translate('Overview over Reactions & Impact'),'url'=>array('action' => 'index', 'page' => null)),
                'inputs-by-question' => array('title' => $this->view->translate('Contributions sorted by Questions'),'url'=>array('action' => 'inputs-by-question', 'page' => null)),
                'tags' => array('title' => $this->view->translate('Contributions sorted by keywords'),'url'=>array('action' => 'tags', 'page' => null))
            );

            $html = '<nav class="offset-bottom-large"><ul class="nav nav-stacked">' . "\n";

            foreach ($navItems as $key => $val) {
                $liClasses = array();

                if ($key == $activeItem) {
                    $liClasses[] = 'active';
                }

                $html .= '<li class="' . implode(' ', $liClasses) . '">';
                $html .= '<a href="'.$this->view->url($val['url']).'">';
                $html .= $val['title'];
                $html .= '</a>';

                if ($key == 'inputs-by-question' && $activeItem == 'inputs-by-question') {
                    $html .= $this->view->QuestionNavigation($activeItemQ, 'follow-up');
                }

                $html .= '</li>' . "\n";
           }

           $html .= '</ul></nav>' . "\n\n";

           return $html;
       }
    }
}
