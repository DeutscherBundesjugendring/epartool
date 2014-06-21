<?php
/**
 * Question Navigation
 *
 * @desc generate link to followup section
 * used in controllers: followup
 * @author Marco Dinnbier
 */
class Zend_View_Helper_FollowupLink extends Zend_View_Helper_Abstract
{
    public function followupLink($inputid, $questionid)
    {
        $con = $this->view->consultation;
        $inputModel = new Model_Inputs();
        $hasFollowup = count($inputModel->getFollowups($inputid) );
        $hasFollowup += count($inputModel->getRelatedWithVotesById( $inputid ));

        if ($hasFollowup) {

                $url = $this->view->url(array('action' => 'show','kid'=>$con->kid, 'qid' => $questionid, 'tid' => $inputid, 'page' => null));
                $html = "<a href=\"$url\">" . $this->view->translate('Reaktionen ansehen') . " <i class=\"icon-angle-right\"></i></a>";

        } else {

                $html = "<span class=\"label\">" . $this->view->translate('Derzeit gibt es keine Reaktionen zu diesem Beitrag.') . "</span>";
        }

        return $html;

    }
}
