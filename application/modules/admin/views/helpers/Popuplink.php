<?php
/**
 * Consultation Navigation
 * Navigation der 3. Ebene: Artikel/Infoseiten zu einer Konsultation
 *
 */
class Admin_View_Helper_Popuplink extends Zend_View_Helper_Abstract
{
    public function Popuplink($url , $title = 'Neues_Fenster', $width = 200, $height = 200, $text = 'Fenster öffnen' , $linktitle = 'Popup öffnen', $classes = '')
    {
        $onclick = "window.open('$url','$title','width=$width,height=$height,resizable=yes,scrollbars=yes');return false;";

        $html = '<a href="#" title="'.$linktitle.'" onclick="'.$onclick.'" class="'.$classes.'" >'.$text.'</a>';

        return $html;
    }
}
