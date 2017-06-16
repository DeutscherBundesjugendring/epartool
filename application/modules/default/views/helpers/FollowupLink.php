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
     * @return string              The outp[ut html
     */
    public function followupLink($inputId, $questionId)
    {
        $inputModel = new Model_Inputs();

        $followupsCount = $inputModel->getFollowupsCount($inputId);
        $relatedWithVotesCount = $inputModel->getRelatedCountById($inputId);
        $relationsCount = $followupsCount + $relatedWithVotesCount;

        if ($relationsCount > 0) {
            $url = $this->view->url([
                'controller' => 'followup',
                'action' => 'show',
                'kid' => $this->view->consultation->kid,
                'qid' => $questionId,
                'tid' => $inputId,
                'page' => null
            ]);

            $html = '<a href="' . $url . '" class="btn btn-default btn-xs hidden-print">';
            $html .= $this->view->translate('View reactions');
            $html .= ' (' . $relationsCount . ')';
            $html .= ' <span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>';
            $html .= '</a>';

            return $html;
        }

        $html = '<br class="hidden-md hidden-lg hidden-print" />';
        $html .= '<br class="hidden-md hidden-lg hidden-print" />';
        $html .= '<span class="text-muted hidden-print">' . $this->view->translate('There are currently no reactions to this contribution.') . '</span>';

        return $html;
    }
}
