<?php
/**
 * Votes
 * @desc    Class of votings, final voting result of consultation
 * @author  Jan Suchandt
 */
class Votes extends Zend_Db_Table_Abstract {
  protected $_name = 'vt_final';
  protected $_primary = array(
    'uid', 'tid'
  );

  protected $_referenceMap = array(
    'Consultations' => array(
      'columns' => 'kid',
      'refTableClass' => 'Consultations',
      'refColumns' => 'kid'
    ),
  );
}

