<?php
/**
 * Votes_Individual
 * @author  Jan Suchandt, Markus Hackel
 */
class Model_Votes_Individual extends Model_DbjrBase {
  protected $_name = 'vt_indiv';
  
  /**
   * get the last vote of an subuser
   * important for back-function of voting
   * @param string $subuid (md5-hash)
   * @return array();
   */
  public function getLastVoteBySubuser($subuid) {
    if(empty($subuid)) {
      return array();
    }
    
    $select = $this->select();
    $select->where('sub_uid LIKE ?', $subuid);
    $select->where('status = ?', 'v');
    $select->order('upd DESC');
    $select->limit(1);
    
    $row = $this->fetchRow($select);
    if(!$row) {
      return $row->toArray();
    }
    else {
      return array();
    }
  }
  
  /**
   * checks if a subuser has allready votet a thesis
   * @param integer $tid
   * @param string $subuid (md5-hash)
   * @return boolean
   */
  public function allreadyVoted($tid, $subuid) {
    if(empty($subuid) || empty($tid)) {
      return false;
    }
    $select = $this->select();
    $select->from(
      $this,
      array(new Zend_Db_Expr('COUNT(*) as count'))
    );
    $select->where('sub_uid=?', $subuid);
    $select->where('tid=?', $tid);
    $select->where('status = ?', 'v');
    
    $row = $this->fetchAll($select)->current();
    if($row->count >0) {
      return true;
    }
    else {
      return false;
    }
  }
  
  public function updateVote($tid, $subUid, $uid, $pts) {
    if(empty($tid) || empty($subUid) || empty($pts) || empty($uid)) {
      return false;
    }

    // check if user has allready votet by this thesis
    if($this->allreadyVoted($tid, $subUid)) {
      // Update vote
      $date = new Zend_Date();

      $select = $this->select();
      $select->where('tid = ?', $tid);
      $select->where('sub_uid = ?', $subUid);

      $row = $this->fetchRow($select);
      $row->pts = $pts;
      $row->upd = $date->get('YYYY-MM-dd HH:mm:ss');
      if($row->save()) {
        return true;
      }
      else {
        return false;
      }

    }
    else {
      // Add vote
      $data = array(
        'uid' => $uid,
        'tid' => $tid,
        'sub_uid' => $subUid,
        'pts' => $pts,
        'status'=>'v'
      );
      $row = $this->createRow($data);
      $row->save();
      if($row) {
        return true;
      }
      else {
        return false;
      }
    }
    
  }
  
  /**
   * Returns array of voting values
   *
   * @param integer $tid
   * @param integer $kid
   * @throws Zend_Validate_Exception
   * @return array Array of voting values array(
   *   'points' => $points,
   *   'cast' => $cast,
   *   'rank' => $rank,
   *  );
   */
  public function getVotingValuesByThesis($tid, $kid) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($tid)) {
      throw new Zend_Validate_Exception('Given parameter tid must be integer!');
    }
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter kid must be integer!');
    }
    $votesRightsModel = new Model_Votes_Rights();
    $points = 0;
    $cast = 0;
    $rank = 0;
    
    $indiv_votes = $this->fetchAll(
      $this->select()
        ->where('tid = ?', $tid)
        ->where('pts < ?', 4)
    );
    $cast = count($indiv_votes);
    
    if ($cast > 0) {
      foreach ($indiv_votes as $indiv_vote) {
        $countIndivByUid = $this->fetchRow(
          $this->select()->from($this->_name, new Zend_Db_Expr('COUNT(*) AS count'))
            ->where('tid = ?', $tid)
            ->where('pts < ?', 4)
            ->where('uid = ?', $indiv_vote['uid'])
        );
        $votesRights = $votesRightsModel->getByUserAndConsultation($indiv_vote['uid'], $kid);
        $indiv_points = ($votesRights['vt_weight']/$countIndivByUid['count']) * $indiv_vote['pts'];
        
        $points += $indiv_points;
      }
      
      $rank = $points / $cast;
    }
    
    return array(
      'points' => $points,
      'cast' => $cast,
      'rank' => (string)$rank,
    );
  }
  
  /**
   * Returns count of individual votes by consultation
   *
   * @param integer $kid
   * @throws Zend_Validate_Exception
   * @return integer
   */
  public function getCountByConsultation($kid) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter kid must be integer!');
    }
    $db = $this->getAdapter();
    $select = $db->select();
    $select->from(array('vi' => $this->_name), new Zend_Db_Expr('COUNT(*) AS count'))
      ->join(array('i' => 'inpt'), 'vi.tid = i.tid', array())
      ->where('i.kid = ?', $kid)
      ->where('vi.pts < ?', 4);
    $stmt = $db->query($select);
    
    return $stmt->fetchColumn();
  }
  
  /**
   * Return the last tid of a subuser
   * @param string $subUid (md5-hash)
   * @return array
   */
  public function getLastBySubuser($subUid) {
    $result = array();
    
    $db = $this->getAdapter();
    $select = $db->select();
    $select->from(array('vi' => 'vt_indiv'));
    $select->joinLeft(array('i' => 'inpt'), 'vi.tid = i.tid');
    $select->where('sub_uid = ?', $subUid);
    $select->order('upd DESC');
    $select->limit(1);
    

    $stmt = $db->query($select);
    $row = $stmt->fetchAll();
    
    if($row) {
      $result = $row;
    }

    return $result;
  }
  
  /**
   * returns the count of voted thesis of a subuser
   * @param string $subUid (md5-hash)
   * @return integer
   */
  public function countVotesBySubuser($subUid) {
    $result = 0;
    
    if(empty($subUid)) {
      return $result;
    }
    
    $select = $this->select()
      ->from($this, array(new Zend_Db_Expr('COUNT(*) as count')))
      ->where('sub_uid = ?', $subUid);
    $row = $this->fetchAll($select)->current();
    if($row) {
      return $row->count;
    }
  }
  
  /**
   * update status of vote of a subuser
   * @param integer $uid
   * @param string $subuid (md5-hash)
   * @param string $status (v = voted, s = skipped, c = confirmed)
   * @param string $statusBefore only by votes with special status (v = voted, s = skipped, c = confirmed)
   */
  public function setStatusForSubuser($uid, $subuid, $status, $statusBefore = '') {
    if(empty($uid) || empty($subuid) || empty($status)) {
      return false;
    }
    if($status != 'v' && $status != 's' && $status != 'c') {
      return false;
    }
    $db = $this->getAdapter();
    
    $data = array(
      'status' => $status,
      'upd'    => new Zend_Db_Expr('NOW()')
    );
    $where = array(
      'uid = ?'  => $uid,
      'sub_uid = ?' => $subuid
    );
    if(!empty($statusBefore) && ($statusBefore=='v' || $statusBefore=='s' || $statusBefore=='c')) {
      $where['status = ?'] = $statusBefore;
    }
//    $db->getProfiler()->setEnabled(true);
    $result = $db->update($this->_name, $data, $where);
//    Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQuery());
//    Zend_Debug::dump($db->getProfiler()->getLastQueryProfile()->getQueryParams());
//    $db->getProfiler()->setEnabled(false);
    if($result) {
      return true;
    }
    return false;
  }
  
  /**
   * Delete votes for user by status
   * @param string $subuid (md5-hash)
   * @param string $status (v = voted, s = skipped, c = confirmed)
   * @param string $statusBefore only by votes with special status (v = voted, s = skipped, c = confirmed)
   */
  public function deleteByStatusForSubuser($uid, $subuid, $status) {
    if(empty($uid) || empty($subuid) || empty($status)) {
      return false;
    }
    if($status != 'v' && $status != 's' && $status != 'c') {
      return false;
    }
    
    $db = $this->getAdapter();
    $where = array(
      'uid = ?'  => $uid,
      'sub_uid = ?' => $subuid,
      'status = ?' => $status,
    );
    $result = $db->delete($this->_name, $where);
    if($result) {
      return true;
    }
    return false;
    
  }
}

