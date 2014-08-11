<?php
/**
 * Decides if a link to the inputs followup chart or static text should be provided
 */
class Zend_View_Helper_FollowupLink extends Zend_View_Helper_Abstract
{
    /**
     * Returns a link to inputs followup chart or static text
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
            $html = "<a href=\"$url\" class=\"article-action\">" . $this->view->translate('Reaktionen ansehen') . " <i class=\"icon-angle-right\"></i></a>";
        } else {
            $html = "<span class=\"label\">" . $this->view->translate('Derzeit gibt es keine Reaktionen zu diesem Beitrag.') . "</span>";
        }

        return $html;
    }
}
