<?php

/**
 * Renders either a link to the inputs reaction_file chart or static text
 */
class Module_Default_View_Helper_FollowupLink extends Zend_View_Helper_Abstract
{
    /**
     * Returns a link to inputs reaction_file chart or static text
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

            $html = '<div class="pull-left text-nowrap offset-right offset-bottom-small">' . "\n";
            $html .= '<a href="' . $url . '" class="link-unstyled link-print-nourl">';
            $html .= '<span class="glyphicon glyphicon-random icon-offset icon-shift-down text-accent" aria-hidden="true"></span>';
            $html .= '</a>' . "\n";
            $html .= '<a href="' . $url . '" class="link-unstyled link-print-nourl">';
            $html .= '<small class="badge' . ($relationsCount > 0 ? ' badge-accent' : '') . '">';
            $html .= $relationsCount;
            $html .= '</small>';
            $html .= '</a>' . "\n";
            $html .= '<a href="' . $url . '" class="btn btn-default btn-xs hidden-print">';
            $html .= $this->view->translate('View reactions');
            $html .= '</a>' . "\n";
            $html .= '</div>' . "\n";

            return $html;
        }

        $html = '<div class="media pull-left offset-bottom-small hidden-print">' . "\n";
        $html .= '<div class="media-left">';
        $html .= '<span class="glyphicon glyphicon-random icon-offset icon-shift-down text-accent" aria-hidden="true"></span>' . "\n";
        $html .= '</div>' . "\n";
        $html .= '<div class="media-body text-muted">';
        $html .= $this->view->translate('There are currently no reactions to this contribution.');
        $html .= '</div>' . "\n";
        $html .= '</div>' . "\n";

        return $html;
    }
}
