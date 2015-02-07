<?php

class Zend_View_Helper_SecondNavigation extends Zend_View_Helper_Abstract
{
    public function secondNavigation($activeItem = null)
    {
        $nowDate = Zend_Date::now();
        $con = $this->view->consultation;
        $disabled = array(
            'article' => false,
            'question' => false,
            'input' => ($nowDate->isEarlier(new Zend_Date($con->inp_fr, Zend_Date::ISO_8601))),
            'follow-up' => (!$nowDate->isLater(new Zend_Date($con->vot_to, Zend_Date::ISO_8601)) || $con->follup_show == 'n'),
            'voting' => ($nowDate->isEarlier(new Zend_Date($con->vot_fr, Zend_Date::ISO_8601)) || $con->vot_to == '0000-00-00 00:00:00'),
        );

        // Voting disable result
        if ($con->vot_to != '0000-00-00 00:00:00' && $nowDate->isLater($con->vot_to) && $con->vot_res_show == 'n') {
            $disabled['voting'] = true;
        }

        $items = array(
            'article' => array(
                'url' => $this->view->baseUrl() . '/article/show/kid/' . $con->kid,
                'text' => '<h2>' . ($con->phase_info ? $this->view->escape($con->phase_info) : $this->view->translate('Info')) . '</h2>',
                'showBubble' => false
            ),
            'question' => array(
                'url' => $this->view->baseUrl() . '/question/index/kid/' . $con->kid,
                'text' => '<h2>' . ($con->phase_support ? $this->view->escape($con->phase_support) : $this->view->translate('Questions')) . '</h2>',
                'showBubble' => false
            ),
            'input' => array(
                'url' => $this->view->baseUrl() . '/input/index/kid/' . $con->kid . '#page-content',
                'text' => '<h2>' . ($con->phase_input ? $this->view->escape($con->phase_input) : $this->view->translate('Contributions')) . '</h2>',
                'showBubble' => false
            ),
            'voting' => array(
                'url' => $this->view->baseUrl() . '/voting/index/kid/' . $con->kid,
                'text' => '<h2>' . ($con->phase_voting ? $this->view->escape($con->phase_voting) : $this->view->translate('Voting')) . '</h2>',
                'showBubble' => false
            ),
            'follow-up' => array(
                'url' => $this->view->baseUrl() . '/followup/index/kid/' . $con->kid,
                'text' => '<h2>' . ($con->phase_followup ? $this->view->escape($con->phase_followup) : $this->view->translate('Reactions & Impact')) . '</h2> <small class="info">' . $this->view->translate('after Voting has ended') . '</small>',
                'showBubble' => false
            ),
        );

        // Add dates
        if ($con->inp_show == 'y') {
            $items['input']['text'].= ' <small class="info">'
                . $this->view->translate('from') . ' '
                . $this->view->formatDate($con->inp_fr, Zend_Date::DATE_MEDIUM)
                . '<br />'
                . $this->view->translate('until') . ' '
                . $this->view->formatDate($con->inp_to, Zend_Date::DATE_MEDIUM)
                . '</small>';
        }
        if ($con->vot_show == 'y') {
            $items['voting']['text'] .= ' <small class="info">'
                . $this->view->translate('from') . ' '
                . $this->view->formatDate($con->vot_fr, Zend_Date::DATE_MEDIUM)
                . '<br />'
                . $this->view->translate('until') . ' '
                . $this->view->formatDate($con->vot_to, Zend_Date::DATE_MEDIUM)
                . '</small>';
        }

        // Add bubbles
        if ($nowDate->isLater(new Zend_Date($con->inp_fr, Zend_Date::ISO_8601))
            && $nowDate->isEarlier(new Zend_Date($con->inp_to, Zend_Date::ISO_8601))
            && $con->inp_show == 'y'
        ) {
            $items['input']['showBubble'] = true;
        }
        if ($nowDate->isLater(new Zend_Date($con->vot_fr, Zend_Date::ISO_8601))
            && $nowDate->isEarlier(new Zend_Date($con->vot_to, Zend_Date::ISO_8601))
            && $con->vot_show == 'y'
        ) {
            $items['voting']['showBubble'] = true;
        }

        // Render
        $html = '<nav role="navigation" class="consultation-nav secondary-navigation">'
                . '<ul class="nav nav-tabs">';
        foreach ($items as $item => $val) {
            $liClasses = array();
            if ($item == $activeItem) {
                $liClasses[] = 'active';
            }
            if ($disabled[$item]) {
                $liClasses[] = 'item-disabled';
            }
            $html .= '<li class="' . implode(' ', $liClasses) . '">';
            if (!empty($val['url']) && !in_array('disabled', $liClasses)) {
                $html .= '<a href="' . $val['url'] . '">';
                $html .= $val['text'];
                if ($val['showBubble']) {
                    $html .= '<span class="bubble bubble-middle"><h3>';
                    if ($item == 'input') {
                        $html .= $this->view->translate('Participate now!');
                    } elseif ($item == 'voting') {
                        $html .= $this->view->translate('Vote now!');
                    }
                    $html .= '</h3></span>';
                }
                $html .= '</a>';
            } else {
                $html .= '<div>' . $val['text'] . '</div>';
            }
            $html .= '</li>';
        }
        $html .= '</ul></nav>';

        return $html;
    }
}
