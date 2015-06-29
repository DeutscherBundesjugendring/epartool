<?php

/**
 * Navigation 3rd Level: Questions of a consultation
 */
class Module_Default_View_Helper_QuestionNavigation extends Zend_View_Helper_Abstract
{

    public function questionNavigation($activeItem = null, $for = NULL, $numbered = false)
    {
        $con = $this->view->consultation;
        $questionModel = new Model_Questions();
        $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();

        switch ($controllerName) {
            case 'voting':
                $urlParams = array();
                break;
            case 'followup':
                $urlParams = array(
                    'action' => 'inputs-by-question',
                    'page' => null,
                );
                break;
            case 'question':
            case 'input':
            default:
                $urlParams = array(
                    'action' => 'show',
                    'page' => null,
                );
                break;
        }

        $items = $questionModel->getByConsultation($con->kid);

        $html = '';

        if ($for !== 'follow-up-box') {

            if ($for !== 'follow-up') {
                $html .= '<nav>' . "\n";
            }

            $html .= '<ul class="nav">' . "\n";
        }

        $i = 1;

        foreach ($items as $item) {
            $number = $numbered ? $i . '. ' : '';
            $liClasses = array();

            if ($item->qi == $activeItem /* || (empty($activeItem) && $i == 1)*/) {
                $liClasses[] = 'active';
            }

            if ($for == 'follow-up-box') {
                $html .= '<p class="offset-bottom-small">';
            } else {
                $html .= '<li class="' . implode(' ', $liClasses) . '">';
            }

            $urlParams['qid'] = $item->qi;
            $html .= '<a href="'
                . $this->view->url($urlParams) . '"'
                . ($for == 'follow-up-box'? ' class="btn btn-default btn-default-alt"'
                    : ' class="question-nav-item" data-qid="' . $item->qi . '"')
                . '>'
                // Number
                . (!empty($item->nr) ? $item->nr . ' ' : '')
                // Frage als Seitentitel im Menü
                . (empty($item->q) ? 'Frage ' . $i : $number . $item->q)
                . '</a>';

            if ($for == 'follow-up-box') {
                $html .= '</p>' . "\n";
            } else {
                $html .= '</li>' . "\n";
            }

            $i++;
        }

        if ($for !== 'follow-up-box') {
            $html .= '</ul>' . "\n";

            if ($for !== 'follow-up') {
                $html .= '</nav>' . "\n";
            }
        }

        return $html;
    }
}
