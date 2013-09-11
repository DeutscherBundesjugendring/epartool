<?php

/**
 * @description adds a Popuplink to Formelement
 *
 * @author Marco Dinnbier
 */
class Admin_Form_Decorator_Popuplink extends Zend_Form_Decorator_Abstract {
    
    protected $test;


    public function render($content) {
        $url = $this->getOption('url');
        $title = $this->getOption('title') ?: 'Neues Fenster';
        $width = $this->getOption('width') ?: 640;
        $height = $this->getOption('height') ?: 720; 
        $text = $this->getOption('text') ?: 'Fenster öffnen';
        $onclick = "window.open('$url','$title','width=$width,height=$height,scrollbars=1');return false;";
       
        $output = '<a href="#" onclick="'.$onclick.'">'.$text.'</a>';
        $placement = $this->getPlacement();
        $separator = $this->getSeparator();
        
        switch ($placement) {
            case 'PREPEND':
            return $output . $separator . $content;
            case 'APPEND':
            default:   return $content . $separator . $output;
        }
        
        
        
    }
    
}

?>

