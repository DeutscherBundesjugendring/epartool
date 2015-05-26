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
        $hasFollowup += $inputModel->fetchRow(
            $inputModel
                ->select()
                ->from($inputModel->info(Model_Inputs::NAME), ['count' => 'COUNT(*) AS count'])
                ->where('tid IN (?)', $relIds)
        )->count;

        if ($hasFollowup) {
            $url = $this->view->url(array('action' => 'show', 'kid'=>$con->kid, 'qid' => $questionId, 'tid' => $inputId, 'page' => null));
            $html = "<a href=\"$url\" class=\"label\">" . $this->view->translate('View reactions') . " <i class=\"icon-angle-right\"></i></a>";
        } else {
            $html = "<span class=\"muted\">" . $this->view->translate('There are currently no reactions to this contribution.') . "</span>";
        }

        return $html;
    }
}
