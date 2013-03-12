<?php
/**
 * Votes_Groups
 * @author Markus Hackel
 *
 */
class Model_Votes_Groups extends Zend_Db_Table_Abstract {
  protected $_name = 'vt_grps';
  protected $_primary = array(
    'uid', 'sub_uid', 'kid'
  );
  
  /**
   * Returns all groups by consultation
   *
   * @param integer $kid
   * @throws Zend_Validate_Exception
   * @return array
   */
  public function getByConsultation($kid) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter kid must be integer!');
    }
    
    $db = $this->getDefaultAdapter();
    $select = $db->select();
    $select->from(array('vg' => $this->_name))
      ->joinUsing(array('u' => 'users'), 'uid')
      ->where('vg.kid = ?', $kid)
      ->order('vg.uid');
      
    $stmt = $db->query($select);
    
    return $stmt->fetchAll();
  }
  
  /**
   * Sets field 'member' to 'n' by given key
   *
   * @param integer $kid
   * @param integer $uid
   * @param string $sub_uid
   * @throws Zend_Validate_Exception
   * @return boolean
   */
  public function denyVoter($kid, $uid, $sub_uid) {
    $intVal = new Zend_Validate_Int();
    $alnumVal = new Zend_Validate_Alnum();
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter kid must be integer!');
    }
    if (!$intVal->isValid($uid)) {
      throw new Zend_Validate_Exception('Given parameter uid must be integer!');
    }
    if (!$alnumVal->isValid($sub_uid)) {
      throw new Zend_Validate_Exception('Given parameter sub_uid must be alphanumerical!');
    }
    
    $row = $this->find($uid, $sub_uid, $kid)->current();
    if ($row) {
      $row->member = 'n';
      $row->save();
      return true;
    } else {
      return false;
    }
  }
  
  /**
   * Sets field 'member' to 'y' by given key
   *
   * @param integer $kid
   * @param integer $uid
   * @param string $sub_uid
   * @throws Zend_Validate_Exception
   * @return boolean
   */
  public function confirmVoter($kid, $uid, $sub_uid) {
    $intVal = new Zend_Validate_Int();
    $alnumVal = new Zend_Validate_Alnum();
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter kid must be integer!');
    }
    if (!$intVal->isValid($uid)) {
      throw new Zend_Validate_Exception('Given parameter uid must be integer!');
    }
    if (!$alnumVal->isValid($sub_uid)) {
      throw new Zend_Validate_Exception('Given parameter sub_uid must be alphanumerical!');
    }
    
    $row = $this->find($uid, $sub_uid, $kid)->current();
    if ($row) {
      $row->member = 'y';
      $row->save();
      return true;
    } else {
      return false;
    }
  }
  
  /**
   * Deletes rows by key and depending rows in vt_indiv
   *
   * @param integer $kid
   * @param integer $uid
   * @param string $sub_uid
   * @throws Zend_Validate_Exception
   * @return integer Number of rows deleted
   */
  public function deleteVoter($kid, $uid, $sub_uid) {
    $intVal = new Zend_Validate_Int();
    $alnumVal = new Zend_Validate_Alnum();
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter kid must be integer!');
    }
    if (!$intVal->isValid($uid)) {
      throw new Zend_Validate_Exception('Given parameter uid must be integer!');
    }
    if (!$alnumVal->isValid($sub_uid)) {
      throw new Zend_Validate_Exception('Given parameter sub_uid must be alphanumerical!');
    }
    
    $votesIndivModel = new Model_Votes_Individual();
    $votesIndivModel->delete(array(
      $votesIndivModel->getAdapter()->quoteInto('uid = ?', $uid),
      $votesIndivModel->getAdapter()->quoteInto('sub_uid = ?', $sub_uid),
    ));
    
    $nr = $this->delete(array(
      $this->getAdapter()->quoteInto('uid = ?', $uid),
      $this->getAdapter()->quoteInto('sub_uid = ?', $sub_uid),
      $this->getAdapter()->quoteInto('kid = ?', $kid),
    ));
    
    return $nr;
  }
}
?>