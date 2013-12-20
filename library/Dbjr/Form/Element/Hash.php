<?php
class Dbjr_Form_Element_Hash extends Zend_Form_Element_Hash {
  
  public function initCsrfValidator()
  {
    $session = $this->getSession();
    if (isset($session->hash)) {
      $rightHash = $session->hash;
    } else {
      $rightHash = null;
    }
  
    $this->addValidator('Identical', true, array($rightHash));
    
    $this->getValidator('Identical')->setMessages(array(
        Zend_Validate_Identical::NOT_SAME => 'Ungültiger Sicherheitstoken',
        Zend_Validate_Identical::MISSING_TOKEN => 'Sicherheitstoken abgelaufen',
    ));
    
    return $this;
  }
}