<?php
/**
 * Consultation Navigation
 * Navigation der 3. Ebene: Artikel/Infoseiten zu einer Konsultation
 *
 */
class Admin_View_Helper_CropText extends Zend_View_Helper_Abstract
{
    public function CropText($string, $maxlength , $croptype = 'normal', $fillchars = '...')
    {
        $result = $string;
        $fcl = mb_strlen( $fillchars );
        $strlen = mb_strlen( $string );

        if ($strlen > $maxlength) {
            if ($croptype === 'middle') {
                $mh = ($maxlength - $fcl) / 2;
                $ml = $mh % 2 !== 0 ? ceil($mh) : $mh;
                $ml2 = $mh % 2 !== 0 ? floor($mh) : $mh;

                $result = mb_substr($string, 0, $ml);
                $result .= $fillchars;
                $result .= mb_substr($string, $strlen - $ml2);
            } else {
                $result = mb_substr($string, 0, $maxlength - $fcl);
                $result .= $fillchars;
            }
        }

        return $result;
    }
}
