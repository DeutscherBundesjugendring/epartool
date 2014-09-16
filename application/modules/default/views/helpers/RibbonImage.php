<?php

class Zend_View_Helper_RibbonImage extends Zend_View_Helper_Abstract
{
    /**
     * Determines which ribbon image to display and produces the html
     * @param  Model_Consultations $con The consultation object
     * @return string              The html of the image
     */
    public function ribbonImage($con)
    {
        $nowDatePlusOne = Zend_Date::now()->addDay(1);
        $nowDate = Zend_Date::now();

        // var_dump($con);exit;
        if ($nowDatePlusOne->isEarlier($con->inp_to)) {
            $imgPart = 'mitmachen';
            $text = $this->view->translate('Participate');
        } elseif ($nowDate->isEarlier($con->inp_to) && $nowDatePlusOne->isLater($con->inp_to)) {
            $imgPart = 'nur-noch-heute';
            $text = $this->view->translate('only today');
        } elseif ($nowDate->isLater($con->inp_to) && $nowDate->isEarlier($con->vot_fr)) {
            $imgPart = 'bald-abstimmen';
            $text = $this->view->translate('Voting coming up');
        } elseif ($nowDate->isLater($con->vot_fr) && $nowDatePlusOne->isEarlier($con->vot_to)) {
            $imgPart = 'mitmachen';
            $text = $this->view->translate('Participate');
        } elseif ($nowDate->isEarlier($con->vot_to) && $nowDatePlusOne->isLater($con->vot_to)) {
            $imgPart = 'nur-noch-heute';
            $text = $this->view->translate('only today');
        } elseif ($nowDate->isLater($con->vot_to) && $con->follup_show == 'n') {
            $imgPart = 'ergebnisse';
            $text = $this->view->translate('Results');
        } elseif ($nowDate->isLater($con->vot_to) && $con->follup_show == 'y') {
            $imgPart = 'reaktion';
            $text = $this->view->translate('Reaction');
        }

        $html = '<span class="label sticker label-' . $imgPart . ' hidden-print">' . $text . '</span>';

        return $html;
    }
}
