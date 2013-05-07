<?php

/**
 * Description of Model Followups
 *
 * @author Marco Dinnbier
 */
class Model_FollowupsSupports extends Zend_Db_Table_Abstract {
  protected $_name = 'fowups_supports';
  protected $_primary = array('fid','tmphash');

  protected $_referenceMap = array(
    'Followups' => array(
      'columns' => 'fid', 'refTableClass' => 'Model_Followups', 'refColumns' => array('fid'),
      'onDelete' => self::CASCADE,
      'onUpdate' => self::CASCADE
    ) 
  );  
}

?>
