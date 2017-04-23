<?php

class Module_Default_View_Helper_RibbonImage extends Zend_View_Helper_Abstract
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
        $text = null;

        if ($nowDatePlusOne->isEarlier(new Zend_Date($con->inp_to, Zend_Date::ISO_8601))) {
            $text = $this->view->translate('Participate');
        } elseif ($nowDate->isEarlier(new Zend_Date($con->inp_to, Zend_Date::ISO_8601))
            && $nowDatePlusOne->isLater(new Zend_Date($con->inp_to, Zend_Date::ISO_8601))
        ) {
            $text = $this->view->translate('only today');
        } elseif ($nowDate->isLater(new Zend_Date($con->inp_to, Zend_Date::ISO_8601))
            && $nowDate->isEarlier(new Zend_Date($con->vot_fr, Zend_Date::ISO_8601))
        ) {
            $text = $this->view->translate('Voting coming up');
        } elseif ($nowDate->isLater(new Zend_Date($con->vot_fr, Zend_Date::ISO_8601))
            && $nowDatePlusOne->isEarlier(new Zend_Date($con->vot_to, Zend_Date::ISO_8601))
        ) {
            $text = $this->view->translate('Participate');
        } elseif ($nowDate->isEarlier(new Zend_Date($con->vot_to, Zend_Date::ISO_8601))
            && $nowDatePlusOne->isLater(new Zend_Date($con->vot_to, Zend_Date::ISO_8601))
        ) {
            $text = $this->view->translate('only today');
        } elseif ($nowDate->isLater(new Zend_Date($con->vot_to, Zend_Date::ISO_8601))
            && !$con->is_followup_phase_showed
        ) {
            $text = $this->view->translate('Results');
        } elseif ($nowDate->isLater(new Zend_Date($con->vot_to, Zend_Date::ISO_8601))
            && $con->is_followup_phase_showed
        ) {
            $text = $this->view->translate('Reaction');
        }

        if ($text) {
            return '<div class="sticker hidden-print"><div class="sticker-label sticker-label-accent">' . $text . '</div></div>';
        }

        return '';
    }
}
