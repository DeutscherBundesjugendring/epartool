<?php

class Module_Default_View_Helper_SecondNavigation extends Zend_View_Helper_Abstract
{
    public function secondNavigation($activeItem = null)
    {
        $nowDate = Zend_Date::now();
        $con = $this->view->consultation;
        $disabled = array(
            'article' => false,
            'question' => false,
            'input' => ($nowDate->isEarlier(new Zend_Date($con->inp_fr, Zend_Date::ISO_8601))),
            'follow-up' => (!$nowDate->isLater(new Zend_Date($con->vot_to, Zend_Date::ISO_8601)) || !$con->is_followup_phase_showed),
            'voting' => ($nowDate->isEarlier(new Zend_Date($con->vot_fr, Zend_Date::ISO_8601)) || !$con->vot_to),
        );

        // Voting disable result
        if ($con->vot_to && $nowDate->isLater($con->vot_to) && !$con->is_voting_result_phase_showed) {
            $disabled['voting'] = true;
        }
                                                    
        $items = array(
            'article' => array(
                'url' => $this->view->baseUrl() . '/article/show/kid/' . $con->kid,
                'text' => $con->phase_info ? $this->view->escape($con->phase_info) : $this->view->translate('Info'),
                'showBubble' => false
            ),
            'question' => array(
                'url' => $this->view->baseUrl() . '/question/index/kid/' . $con->kid,
                'text' => $con->phase_support ? $this->view->escape($con->phase_support) : $this->view->translate('Questions'),
                'showBubble' => false
            ),
            'input' => array(
                'url' => $this->view->baseUrl() . '/input/index/kid/' . $con->kid . '#page-content',
                'text' => $con->phase_input ? $this->view->escape($con->phase_input) : $this->view->translate('Contributions'),
                'showBubble' => false
            ),
            'voting' => array(
                'url' => $this->view->baseUrl() . '/voting/index/kid/' . $con->kid,
                'text' => $con->phase_voting ? $this->view->escape($con->phase_voting) : $this->view->translate('Voting'),
                'showBubble' => false
            ),
            'follow-up' => array(
                'url' => $this->view->baseUrl() . '/followup/index/kid/' . $con->kid,
                'text' => $con->phase_followup ? $this->view->escape($con->phase_followup) : $this->view->translate('Reactions & Impact'),
                'showBubble' => false
            ),
        );

        // Add dates
        if ($con->is_input_phase_showed) {
            $items['input']['info'] = $this->view->translate('from') . ' '
                . $this->view->formatDate($con->inp_fr, Zend_Date::DATE_MEDIUM)
                . '<br />'
                . $this->view->translate('until') . ' '
                . $this->view->formatDate($con->inp_to, Zend_Date::DATE_MEDIUM);
        }
        if ($con->is_voting_phase_showed) {
            $items['voting']['info'] = $this->view->translate('from') . ' '
                . $this->view->formatDate($con->vot_fr, Zend_Date::DATE_MEDIUM)
                . '<br />'
                . $this->view->translate('until') . ' '
                . $this->view->formatDate($con->vot_to, Zend_Date::DATE_MEDIUM);
        }

        // Add bubbles
        if ($nowDate->isLater(new Zend_Date($con->inp_fr, Zend_Date::ISO_8601))
            && $nowDate->isEarlier(new Zend_Date($con->inp_to, Zend_Date::ISO_8601))
            && $con->is_input_phase_showed
        ) {
            $items['input']['showBubble'] = true;
        }
        if ($nowDate->isLater(new Zend_Date($con->vot_fr, Zend_Date::ISO_8601))
            && $nowDate->isEarlier(new Zend_Date($con->vot_to, Zend_Date::ISO_8601))
            && $con->is_voting_phase_showed
        ) {
            $items['voting']['showBubble'] = true;
        }

        // Render
        $html  = '<nav class="hidden-print">' . "\n";
        $html .= '<ul class="consultation-phases consultation-phases-full">' . "\n";

        foreach ($items as $item => $val) {
            $liClasses = array();

            if ($item == $activeItem) {
                $liClasses[] = 'active';
            }

            if ($disabled[$item]) {
                $liClasses[] = 'disabled';
            }

            $html .= '<li>';

            if (!empty($val['url']) && !in_array('disabled', $liClasses)) {
                $html .= '<a href="' . $val['url'] . '" class="consultation-phases-item consultation-phases-full-item ' . implode($liClasses) . '">' . "\n";
                $html .= '<div class="consultation-phases-item-title">' . $val['text'] . '</div>' . "\n";

                if (isset($val['info'])) {
                    $html .= '<div class="consultation-phases-item-info"><small>' . $val['info'] . '</small></div>' . "\n";
                }

                if ($val['showBubble']) {
                    $html .= '<div class="bubble consultation-phases-item-bubble">';
                    $html .= '<h4 class="bubble-title">';

                    if ($item == 'input') {
                        $html .= $this->view->translate('Participate now!');
                    } elseif ($item == 'voting') {
                        $html .= $this->view->translate('Vote now!');
                    }

                    $html .= '</h4>';
                    $html .= '</div>' . "\n";
                }

                $html .= '</a>' . "\n";
            } else {
                $html .= '<div class="consultation-phases-item ' . implode($liClasses) . '">' . "\n";
                $html .= '<div class="consultation-phases-item-title">' . $val['text'] . '</div>' . "\n";

                if (isset($val['info'])) {
                    $html .= '<div class="consultation-phases-item-info"><small>' . $val['info'] . '</small></div>' . "\n";
                }

                $html .= '</div>' . "\n";
            }

            $html .= '</li>' . "\n";
        }
        $html .= '</ul>' . "\n";
        $html .= '</nav>' . "\n\n";

        return $html;
    }
}
