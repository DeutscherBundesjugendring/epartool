<?php

class Module_Default_View_Helper_Perex extends Zend_View_Helper_Abstract
{
    /**
     * Creates a perex from string
     * @param  string $string The original string
     * @param  int $length    The desired length
     * @param  array  $conf   Configuration array. Takes following flags: HTML_ENTITY_DECODE, STRIP_TAGS, STRIPSLASHES
     * @return string         The shortened string
     */
    public function perex($string, $length, $conf = array())
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

        if (mb_strlen($string) <= $length) {
            return $string;
        } else {
            return mb_substr($string, 0, mb_strpos($string, ' ', $length)) . '...';
        }
    }
}
