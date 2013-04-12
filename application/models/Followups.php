<?php

/**
 * Description of Model Followups
 *
 * @author Marco Dinnbier
 */
class Model_Followups extends Zend_Db_Table_Abstract {
  protected $_name = 'fowup_fls';
  protected $_primary = 'ffid';
  
  public function getByKid($kid, $order = NULL, $limit = NULL) {
    //$result = array();
    
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($kid)) {
      return array();
    }
    $select = $this->select();
    $select->where('kid=?', $kid);
    
    if ($order) {
        $select->order($order);        
    }
    if ($limit) {
        
        $select->limit($limit);        
    }
    $result = $this->fetchAll($select);
    return $result->toArray();
    
  }
  
}

?>
