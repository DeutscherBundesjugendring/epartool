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

        if ($nowDatePlusOne->isEarlier(new Zend_Date($con->inp_to, Zend_Date::ISO_8601))) {
            $imgPart = 'mitmachen';
            $text = $this->view->translate('Participate');
        } elseif ($nowDate->isEarlier(new Zend_Date($con->inp_to, Zend_Date::ISO_8601))
            && $nowDatePlusOne->isLater(new Zend_Date($con->inp_to, Zend_Date::ISO_8601))
        ) {
            $imgPart = 'nur-noch-heute';
            $text = $this->view->translate('only today');
        } elseif ($nowDate->isLater(new Zend_Date($con->inp_to, Zend_Date::ISO_8601))
            && $nowDate->isEarlier(new Zend_Date($con->vot_fr, Zend_Date::ISO_8601))
        ) {
            $imgPart = 'bald-abstimmen';
            $text = $this->view->translate('Voting coming up');
        } elseif ($nowDate->isLater(new Zend_Date($con->vot_fr, Zend_Date::ISO_8601))
            && $nowDatePlusOne->isEarlier(new Zend_Date($con->vot_to, Zend_Date::ISO_8601))
        ) {
            $imgPart = 'mitmachen';
            $text = $this->view->translate('Participate');
        } elseif ($nowDate->isEarlier(new Zend_Date($con->vot_to, Zend_Date::ISO_8601))
            && $nowDatePlusOne->isLater(new Zend_Date($con->vot_to, Zend_Date::ISO_8601))
        ) {
            $imgPart = 'nur-noch-heute';
            $text = $this->view->translate('only today');
        } elseif ($nowDate->isLater(new Zend_Date($con->vot_to, Zend_Date::ISO_8601))
            && $con->follup_show == 'n'
        ) {
            $imgPart = 'ergebnisse';
            $text = $this->view->translate('Results');
        } elseif ($nowDate->isLater(new Zend_Date($con->vot_to, Zend_Date::ISO_8601))
            && $con->follup_show == 'y'
        ) {
            $imgPart = 'reaktion';
            $text = $this->view->translate('Reaction');
        }

        $html = '<span class="label sticker label-' . $imgPart . ' hidden-print">' . $text . '</span>';

        return $html;
    }
}
