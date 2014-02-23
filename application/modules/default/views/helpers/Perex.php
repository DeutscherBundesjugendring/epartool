<?php

class Zend_View_Helper_Perex extends Zend_View_Helper_Abstract
{
    /**
     * creates a perex from string
     * @param  [type] $string [description]
     * @param  [type] $length [description]
     * @param  array  $conf   [description]
     * @return [type]         [description]
     */
    public function perex($string, $length, $conf=array())
    {
        if (in_array('HTML_ENTITY_DECODE', $conf)) {
            $string = html_entity_decode($string);
        }
        if (in_array('STRIP_TAGS', $conf)) {
            $string = strip_tags($string);
        }
        if (in_array('STRIPSLASHES', $conf)) {
            $string = stripslashes($string);
        }

        if (strlen($string) <= $length) {
            return $string;
        } else {
            return substr($string, 0, strpos($string, ' ', $length)) . '...';
        }
    }
}
