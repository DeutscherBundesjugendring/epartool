<?php
class Model_User_Info extends Model_DbjrBase {
  protected $_name = 'user_info';
  protected $_primary = 'user_info_id';
  
  protected $_referenceMap = array(
      'Users' => array(
          'columns' => 'uid',
          'refTableClass' => 'Model_Users',
          'refColumns' => 'uid'
      ),
      'Consultations' => array(
          'columns' => 'kid',
          'refTableClass' => 'Model_Consultations',
          'refColumns' => 'kid'
      )
  );
}
?>