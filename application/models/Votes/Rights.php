<?php
/**
 * VotesRights
 * @desc    Class of voting-rights
 * @author  Jan Suchandt
 */
class Votes_Rights extends Zend_Db_Table_Abstract {
  protected $_name = 'vt_rights';
  protected $_primary = array(
    'uid', 'kid'
  );

  protected $_referenceMap = array(
    'Users' => array(
      'columns' => 'uid', 'refTableClass' => 'Users', 'refColumns' => 'uid'
    ),
    'Consultations' => array(
      'columns' => 'kid',
      'refTableClass' => 'Consultations',
      'refColumns' => 'kid'
    ),
  );
}

