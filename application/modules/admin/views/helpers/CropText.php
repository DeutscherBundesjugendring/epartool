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
        $fcl = strlen( $fillchars );
        $strlen = strlen( $string );

        if ($strlen > $maxlength) {
            if ($croptype === 'middle') {
                $mh = ($maxlength - $fcl) / 2;
                $ml = $mh % 2 !== 0 ? ceil($mh) : $mh;
                $ml2 = $mh % 2 !== 0 ? floor($mh) : $mh;

                $result = substr($string, 0, $ml);
                $result .= $fillchars;
                $result .= substr($string, $strlen - $ml2);
            } else {
                $result = substr($string, 0, $maxlength - $fcl);
                $result .= $fillchars;
            }
        }

        return $result;
    }
}
