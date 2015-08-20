<?php

/**
 * Renders either a link to the inputs follow-up chart or static text
 */
class Module_Default_View_Helper_FollowupLink extends Zend_View_Helper_Abstract
{
    /**
     * Returns a link to inputs follow-up chart or static text
     * @param  integer $inputId    The input identifier
     * @param  integer $questionId The question identifier
     * @param  array   $relIds     Array of related input ids
     * @return string              The outp[ut html
     */
    public function followupLink($inputId, $questionId, $relIds)
    {
        $con = $this->view->consultation;
        $inputModel = new Model_Inputs();
        $hasFollowup = count($inputModel->getFollowups($inputId));
        $hasFollowup += (int) $inputModel->fetchRow(
            $inputModel
                ->select()
                ->from($inputModel->info(Model_Inputs::NAME), ['count' => 'COUNT(*) AS count'])
                ->where('tid IN (?)', $relIds)
        )->count;

        if ($hasFollowup) {
            $url = $this->view->url(['action' => 'show', 'kid' => $con->kid, 'qid' => $questionId, 'tid' => $inputId, 'page' => null]);

            $html = '<a href="' . $url . '" class="btn btn-default btn-xs hidden-print">';
            $html .= $this->view->translate('View reactions');
            $html .= ' <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>';
            $html .= '</a>';
        } else {
            $html = '<br class="hidden-md hidden-lg hidden-print" />';
            $html .= '<br class="hidden-md hidden-lg hidden-print" />';
            $html .= '<span class="text-muted hidden-print">' . $this->view->translate('There are currently no reactions to this contribution.') . '</span>';
        }

        return $html;
    }
}
