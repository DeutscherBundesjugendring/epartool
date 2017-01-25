<?php

class Module_Default_View_Helper_Perex extends Zend_View_Helper_Abstract
{
    /**
     * Creates a perex from string
     * @param  string $string The original string
     * @param  int $length    The desired length
     * @return string         The shortened string
     */
    public function perex($string, $length)
    {
        if (mb_strlen($string) <= $length) {
            return $string;
        } else {
            return mb_substr($string, 0, mb_strpos($string, ' ', $length)) . '...';
        }
    }
}
