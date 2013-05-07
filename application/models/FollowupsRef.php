<?php

/**
 * Description of Model Followups
 *
 * @author Marco Dinnbier
 */
class Model_FollowupsRef extends Zend_Db_Table_Abstract {
  protected $_name = 'fowups_rid';
  protected $_primary = array(
    'fid_ref', 'tid', 'ffid','fid'
  ); 

  protected $_referenceMap = array(
    'Followups_Ref' => array(
      'columns' => 'fid_ref', 'refTableClass' => 'Model_Followups', 'refColumns' => 'fid',
      'onDelete' => self::CASCADE,
      'onUpdate' => self::CASCADE
    ),
    'Followups' => array(
      'columns' => 'fid', 'refTableClass' => 'Model_Followups', 'refColumns' => 'fid',
      'onDelete' => self::CASCADE,
      'onUpdate' => self::CASCADE
    ),
    'FollowupFiles' => array(
      'columns' => 'ffid', 'refTableClass' => 'Model_FollowupFiles', 'refColumns' => 'ffid',
      'onDelete' => self::CASCADE,
      'onUpdate' => self::CASCADE
    ),
    'Inputs' => array(
      'columns' => 'tid', 'refTableClass' => 'Model_Inputs', 'refColumns' => 'tid',
      'onDelete' => self::CASCADE,
      'onUpdate' => self::CASCADE
    ),
  );
  
  
  
  
}

?>
