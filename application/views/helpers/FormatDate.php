<?php

class Application_View_Helper_FormatDate extends Zend_View_Helper_Abstract
{
    /**
     * Returns formated datetime
     * @param  string $dateTimeString The datetime passable to Zend_Date::set() method
     * @param  string $format         The format passable to Zend_Date::get() method
     * @return string                 The fomrated datetime
     */
    public function formatDate($dateTimeString, $format = null)
    {
        if ($format == null) {
            $format = Zend_Date::DATETIME_MEDIUM;
        }

        return (new Zend_Date($dateTimeString, Zend_Date::ISO_8601))->get($format);
    }
}
