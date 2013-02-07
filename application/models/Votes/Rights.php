<?php
/**
 * VotesRights
 * @desc    Class of voting-rights
 * @author  Jan Suchandt
 */
class Model_Votes_Rights extends Zend_Db_Table_Abstract {
  protected $_name = 'vt_rights';
  protected $_primary = array(
    'uid', 'kid'
  );

  protected $_referenceMap = array(
    'Users' => array(
      'columns' => 'uid', 'refTableClass' => 'Model_Users', 'refColumns' => 'uid'
    ),
    'Consultations' => array(
      'columns' => 'kid',
      'refTableClass' => 'Model_Consultations',
      'refColumns' => 'kid'
    ),
  );
}

