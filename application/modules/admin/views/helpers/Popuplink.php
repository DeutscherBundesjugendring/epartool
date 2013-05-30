<?php
/**
 * Consultation Navigation
 * Navigation der 3. Ebene: Artikel/Infoseiten zu einer Konsultation
 *
 */
class Admin_View_Helper_Popuplink extends Zend_View_Helper_Abstract {
  
  public function Popuplink( $url , $title = 'Neues Fenster', $width = 200, $height = 200, $text = 'Fenster öffnen' , $linktitle='Popup öffnen', $style=NULL) {
   
     //if (!$urlarray) return   
     
   
    
    $style = $style ? $style : '';
    
    $onclick = "window.open('$url','$title','width=$width,height=$height, scrollbars=yes');return false;";  
      
    $html = '<a style="'.$style.'" href="#" title="'.$linktitle.'" onclick="'.$onclick.'">'.$text.'</a>';
   
    return $html;
  }
}
?>