<?php
/**
 * Entries
 * @desc    Class of Inputs, userentries to questions of a consultation
 * @author  Jan Suchandt
 */
class Model_Inputs extends Model_DbjrBase {
  protected $_name = 'inpt';
  protected $_primary = 'tid';
  
  protected $_dependentTables = array('Model_InputsTags');

  protected $_referenceMap = array(
    'Questions' => array(
      'columns' => 'qi', 'refTableClass' => 'Model_Questions', 'refColumns' => 'qi'
    )
  );
  
  protected $_flashMessenger = null;
  
  protected $_auth = null;
  
  public function init() {
    $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
    $this->_auth = Zend_Auth::getInstance();
  }

  /**
   * getById
   * @desc returns entry by id
   * @name getById
   * @param integer $id
   * @param integer $tag [optional] Filter nach Tag
   * @return array
   */
  public function getById($id, $tag = 0) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return array();
    }

    $row = $this->find($id)->current();
    $subrow1 = $row->findManyToManyRowset('Model_Tags', 'Model_InputsTags')->toArray();

    $result = $row->toArray();
    $result['tags'] = $subrow1;
    if ($tag > 0) {
      // Ergebnisdatensatz nur zurückliefern, wenn dieser den angegebenen Tag zugeordnet hat
      $deliverRecord = false;
      foreach ($result['tags'] as $value) {
        if ($tag == $value['tg_nr']) {
          $deliverRecord = true;
        }
      }
      if (!$deliverRecord) {
        $result = null;
      }
    }

    return $result;
  }

  /**
   * add
   * @desc add new entry to db-table
   * @name add
   * @param array $data
   * @return integer primary key of inserted entry
   *
   */
  public function add($data) {
    $row = $this->createRow($data);

    return (int)$row->save();
  }

  /**
   * updateById
   * @param integer $id
   * @param array $data
   * @return integer
   *
   */
  public function updateById($id, $data) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return 0;
    }
    // exists?
    if ($this->find($id)->count() < 1) {
      return 0;
    }
    
    if (isset($data['tags']) && !empty($data['tags'])) {
      // Tag Zuordnungen speichern
      $modelInputsTags = new Model_InputsTags();
      $modelInputsTags->deleteByInputsId($id);
      $inserted = $modelInputsTags->insertByInputsId($id, $data['tags']);
    }
    
    $row = $this->find($id)->current();
    $row->setFromArray($data);
    return $row->save();
  }

  /**
   * deleteById
   * @desc delete entry by id
   * @name deleteById
   * @param integer $id
   * @return integer
   */
  public function deleteById($id) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return 0;
    }
    // exists?
    if ($this->find($id)->count() < 1) {
      return 0;
    }

    // where
    $where = $this->getDefaultAdapter()
        ->quoteInto($this->_primary[1] . '=?', $id);
    $result = $this->delete($where);
    return $result;
  }

  /**
   * getByUser
   * @desc returns entry by user-id
   * @name getByUser
   * @param integer $uid id of user
   * @return array
   */
  public function getByUser($uid) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($uid)) {
      return 0;
    }

    // fetch
    $select = $this->select();
    $select->where('uid=?', $uid);
    $result = $this->fetchAll($select);
    return $result->toArray();
  }
  
  /**
   * Returns entries by user and consultation
   *
   * @param integer $uid User ID
   * @param integer $kid Consultation ID
   * @param string|array $order [optional] Order specification
   */
  public function getByUserAndConsultation($uid, $kid, $order = null) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($uid)) {
      return 0;
    }
    if (!$validator->isValid($kid)) {
      return 0;
    }

    // fetch
    $select = $this->select();
    $select->where('uid=?', $uid);
    $select->where('kid=?', $kid);
    if ($order) {
      $select->order($order);
    }
    $result = $this->fetchAll($select);
//    return $result->toArray();
    return $result;
  }

  /**
   * getByQuestion
   * @desc returns entry by question-id
   * @name getByQuestion
   * @param integer $qid id of question (qi in mysql-table)
   * @param string|array $order [optional] MySQL Order Expression, e.g. 'votes DESC'
   * @param integer $limit [optional] Number of records to return
   * @param integer $tag [optional] id of tag (tg_nr)
   * @return array
   */
  public function getByQuestion($qid, $order = 'i.tid ASC', $limit = null, $tag = null) {
    // is int?
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($qid)) {
      return array();
    }

    // get select obj
    $select = $this->getSelectByQuestion($qid, $order, $limit, $tag);
    
    $stmt = $this->getDefaultAdapter()->query($select);
    $result = $stmt->fetchAll();
    return $result;
  }
  
  /**
   * Returns number of inputs for a consultation
   *
   * @param integer $kid
   * @param boolean $excludeInvisible [optional], Default: true
   * @return integer
   */
  public function getCountByConsultation($kid, $excludeInvisible = true) {
    $select = $this->select()
      ->from($this, array(new Zend_Db_Expr('COUNT(*) as count')))
      ->where('kid = ?', $kid)
      ->where('uid <> ?', 1);
      
    if ($excludeInvisible) {
      $select->where('block<>?', 'y')
        ->where('user_conf=?', 'c');
    }
    
    $row = $this->fetchAll($select)->current();
    return $row->count;
  }
  
  /**
   * Returns number of inputs for a user
   *
   * @param integer $uid
   * @return integer
   */
  public function getCountByUser($uid) {
    $select = $this->select()
      ->from($this, array(new Zend_Db_Expr('COUNT(*) as count')))
      ->where('uid = ?', $uid);
    
    $row = $this->fetchAll($select)->current();
    return $row->count;
  }
  
  /**
   * Returns number of inputs for a consultation, filtered by given conditions
   *
   * @param integer $kid
   * @param array $filter [optional] array(array('field' => $field, 'operator' => $operator, 'value' => $value)[, ...])
   * @return integer
   */
  public function getCountByConsultationFiltered($kid, $filter = array()) {
    $select = $this->select()
      ->from($this, array(new Zend_Db_Expr('COUNT(*) as count')))
      ->where('kid = ?', $kid);
      // JSU Superadmin wird eigentlich ausgenommen, im Altsystem ist es aber nicht so
      //->where('uid <> ?', 1);
      
    foreach ($filter as $condition) {
      if (is_array($condition)) {
        $select->where(
          $this->getDefaultAdapter()->quoteIdentifier($condition['field']) . ' '
          . $condition['operator'] . ' ?',
          $condition['value']);
      }
    }
    $row = $this->fetchAll($select)->current();
    return $row->count;
  }
  
	/**
   * Returns number of inputs for a question
   *
   * @param integer $qid
   * @param integer $tag [optional]
   * @param boolean $excludeInvisible [optional], Default: true
   * @return integer
   */
  public function getCountByQuestion($qid, $tag = null, $excludeInvisible = true) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($qid)) {
      throw new Zend_Validate_Exception('Given parameter qid must be integer!');
    }
    
    $db = $this->getDefaultAdapter();
    $select = $db->select();
    $select->from(array('i' => $this->_name),
      array(new Zend_Db_Expr('COUNT(*) as count')))
      ->where('i.qi = ?', $qid);
    if ($excludeInvisible) {
      // nur nicht geblockte:
      $select->where('i.block<>?', 'y')
        // nur bestätigte:
        ->where('i.user_conf=?', 'c');
    }
    
    if ($intVal->isValid($tag)) {
      $select->joinLeft(array('it' => 'inpt_tgs'), 'i.tid = it.tid', array());
      $select->where('it.tg_nr = ?', $tag);
    }
    
    $stmt = $db->query($select);
    $row = $stmt->fetch();
    
    return $row['count'];
  }
  
  /**
   * Returns number of inputs for a question, filtered by given conditions
   *
   * @param integer $qid
   * @param array $filter [optional] array(array('field' => $field, 'operator' => $operator, 'value' => $value)[, ...])
   * @return integer
   */
  public function getCountByQuestionFiltered($qid, $filter = array()) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($qid)) {
      throw new Zend_Validate_Exception('Given parameter qid must be integer!');
    }
    
    $select = $this->select()
      ->from($this, array(new Zend_Db_Expr('COUNT(*) as count')))
      ->where('qi = ?', $qid)
      ->where('uid <> ?', 1);
    
    if (!empty($filter)) {
      foreach ($filter as $condition) {
        if (is_array($condition)) {
          $select->where(
            $this->getDefaultAdapter()->quoteIdentifier($condition['field']) . ' '
            . $condition['operator'] . ' ?',
            $condition['value']);
        }
      }
    }
      
    $row = $this->fetchAll($select)->current();
    return $row->count;
  }
  
  /**
   * Returns Zend_Db_Select for use in e.g. Paginator
   *
   * @param integer $qid
   * @param string|array $order
   * @param integer $limit
   * @param integer $tag [optional] id of tag (tg_nr)
   * @return Zend_Db_Select
   */
  public function getSelectByQuestion($qid, $order = 'i.tid DESC', $limit = null, $tag = null) {
    $intVal = new Zend_Validate_Int();
    $db = $this->getDefaultAdapter();
    $select = $db->select();
    $select->from(array('i' => $this->_name));
    
    if ($intVal->isValid($tag)) {
      $select->joinLeft(array('it' => 'inpt_tgs'), 'i.tid = it.tid', array());
      $select->where('it.tg_nr = ?', $tag);
    }
    
    $select->where('i.qi=?', $qid)->where('i.block<>?', 'y')->where('i.user_conf=?', 'c');
    
    if (!empty($order)) {
      $select->order($order);
    }
    
    if ($intVal->isValid($limit)) {
      $select->limit($limit);
    }
    
    return $select;
  }
  
  /**
   * Stores inputs from session into database
   *
   * @param integer $uid
   * @throws Zend_Validate_Exception
   * @return void
   */
  public function storeSessionInputsInDb($uid) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($uid)) {
      throw new Zend_Validate_Exception('Given uid must be integer!');
    }
    $inputCollection = new Zend_Session_Namespace('inputCollection');
    if (!empty($inputCollection)) {
      foreach ($inputCollection->inputs as $input) {
        // mit uid speichern
        $input['uid'] = $uid;
        $this->add($input);
      }
      // Session inputs löschen
      unset($inputCollection->inputs);
    }
  }
  
  /**
   * Retruns all unconfirmed inputs by user
   *
   * @param integer $uid
   * @throws Zend_Validate_Exception
   * @return Zend_Db_Table_Rowset
   */
  public function getUnconfirmedByUser($uid) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($uid)) {
      throw new Zend_Validate_Exception('Given user id has to be integer!');
      return null;
    }
    $select = $this->select();
    $select->where('uid=?', $uid)->where('user_conf=?', 'u');
    $select->order('when');
    
    return $this->fetchAll($select);
  }
  
  /**
   * Generates, saves and returns a key for input confirmation
   *
   * @param integer $id Input ID
   * @throws Zend_Exception
   * @return string
   */
  public function generateConfirmationKey($id) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($id)) {
      throw new Zend_Exception('Given tid must be integer!');
      return null;
    }
    $row = $this->find($id)->current();
    if (!empty($row) && $row->user_conf != 'c') {
      $key = md5($id . time() . getenv('REMOTE_ADDR') . mt_rand());
      $row->confirm_key = $key;
      $row->save();
      return $key;
    } else {
      return null;
    }
  }
  
  /**
   * Processes input confirmation request
   *
   * @param string $ckey
   * @throws Zend_Validate_Exception
   * @return boolean true on success
   */
  public function confirmByCkey($ckey) {
    $return = false;
    $alnumVal = new Zend_Validate_Alnum();
    if (!$alnumVal->isValid($ckey)) {
      throw new Zend_Validate_Exception();
      return $return;
    }
    $select = $this->select();
    $select->where('confirm_key = ?', $ckey);
    $row = $this->fetchAll($select)->current();
    if (!empty($row)) {
      $return = true;
      $row->user_conf = 'c';
      $row->confirm_key = '';
      $row->save();
      $this->_flashMessenger->addMessage('Vielen Dank! Dein Beitrag wurde bestätigt!', 'success');
    }
    return $return;
  }
  
  /**
   * Deletes several entries at once
   *
   * @param array $ids Array of integer values (Input IDs)
   * @return integer Number of deleted entries
   */
  public function deleteBulk($ids) {
    $nrDeleted = 0;
    if (is_array($ids) && !empty($ids)) {
      foreach ($ids as $id) {
        $nr = $this->deleteById($id);
        $nrDeleted+= $nr;
      }
    }
    return $nrDeleted;
  }
  
  /**
   * Saves changes for several entries at once
   *
   * @param array $ids Array of integer values (Input IDs)
   * @param array $data Key value pairs
   * @return void
   */
  public function editBulk($ids, $data) {
    if (is_array($ids) && !empty($ids)) {
      foreach ($ids as $id) {
        $this->updateById($id, $data);
      }
    }
  }
  
  /**
   * Returns inputs by user and consultation grouped by question
   * for the user inputs overview
   *
   * @param integer $uid
   * @param integer $kid
   * @return array
   */
  public function getUserEntriesOverview($uid, $kid) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($uid)) {
      throw new Zend_Validate_Exception('Given kid must be integer!');
    }
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given uid must be integer!');
    }
    $entries = array();
    $entriesRaw = $this->getByUserAndConsultation($uid, $kid);
    
    $questionModel = new Model_Questions();
    $questions = $questionModel->getByConsultation($kid);
    foreach ($questions as $question) {
      $entries[$question['qi']] = $question->toArray();
    }
    foreach ($entriesRaw as $rawEntry) {
      $entries[$rawEntry['qi']]['inputs'][] = $rawEntry->toArray();
    }
    
    return $entries;
  }
  
  /**
   * Returns entries ordered by input date descending
   *
   * @param integer $limit
   * @throws Zend_Validate_Exception
   * @return Zend_Db_Table_Rowset
   */
  public function getLast($limit = 10) {
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($limit)) {
      throw new Zend_Validate_Exception('Given limit has to be integer!');
    }
    $select = $this->select();
    $select
      ->order('when DESC')
      ->limit($limit);
    return $this->fetchAll($select);
  }
  
  /**
   * Returns formatted CSV string
   *
   * @param integer $kid
   * @param integer $qid
   * @param string $mod
   * @param integer $tag [optional]
   * @throws Zend_Validate_Exception
   * @return string
   */
  public function getCSV($kid, $qid, $mod, $tag = null) {
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter kid must be integer!');
    }
    if (!$validator->isValid($qid)) {
      throw new Zend_Validate_Exception('Given parameter qid must be integer!');
    }
    if (!empty($tag)) {
      if (!$validator->isValid($tag)) {
        throw new Zend_Validate_Exception('Given parameter tag must be integer!');
      }
    }
    $csv = '';
    $db = $this->getAdapter();
    $select = $db->select();
    $select->from(array('i' => 'inpt'));
    $select->where('kid = ?', $kid);
    $select->where('qi = ?', $qid);
    switch ($mod) {
      case 'cnf':
        $select->where('i.user_conf = ?', 'c');
        break;
      case 'unc':
        $select->where('i.user_conf = ?', 'u');
        break;
      case 'all':
        $select->where('i.uid > ?', 1);
        break;
      case 'vot':
        $select->where('i.vot = ?', 'y');
        break;
      case 'edt':
        $select->where('i.uid = ?', 1);
        break;
    }
    $select->joinLeft(
      array('it' => 'inpt_tgs'),
      'i.tid = it.tid',
      array()
    );
    $select->joinLeft(
      array('t' => 'tgs'),
      'it.tg_nr = t.tg_nr',
      array('tags' => "GROUP_CONCAT(t.tg_de ORDER BY t.tg_de SEPARATOR ',')")
    );
    $select->group('i.tid');
    
    if (!empty($tag)) {
      $select->where('it.tg_nr = ?', $tag);
    }
    
    $consultationModel = new Model_Consultations();
    $consultation = $consultationModel->find($kid)->current()->toArray();
    if (!empty($consultation)) {
      $csv.= '"Konsultation";"' . $consultation['titl'] . '"' . "\r\n";
    } else {
      return 'Konsultation nicht gefunden!';
    }
    
    $questionModel = new Model_Questions();
    $question = $questionModel->find($qid)->current()->toArray();
    if (!empty($question)) {
      $csv.= '"' .$question['nr'] . '";"' . $question['q'] . '"' . "\r\n\r\n";
    } else {
      return 'Frage nicht gefunden!';
    }
    
    $csv.='"THESEN-ID";"BEITRAG";"ERKLÄRUNG";"SCHLAGWÖRTER";"NOTIZEN"' . "\r\n";
    
    $stmt = $db->query($select);
    $rowSet = $stmt->fetchAll();
    foreach ($rowSet as $row) {
      $csv.='"' . $row['tid'] . '";"'
        . $row['thes'] . '";"'
        . $row['expl'] . '";"'
        . $row['tags'] . '"' . "\r\n";
    }
    
    return $csv;
  }
  
  /**
   * Adds one point to support counter of given inputs ID
   * and returns the new number of supports
   *
   * @param integer $tid
   * @return integer
   */
  public function addSupport($tid) {
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($tid)) {
      throw new Zend_Validate_Exception('Given parameter tid must be integer!');
    }
    $countSupports = 0;
    $row = $this->find($tid)->current();
    if ($row) {
      $consultationModel = new Model_Consultations();
      $consultation = $consultationModel->find($row->kid)->current();
      if (Zend_Date::now()->isLater($consultation->spprt_fr)
        && Zend_Date::now()->isEarlier($consultation->spprt_to)) {
        $row->spprts++;
        $row->save();
      }
      $countSupports = $row->spprts;
    }
    
    return $countSupports;
  }
  
  /**
   * Search in questions by consultations
   * @param string $needle
   * @param integer $consultationId
   * @param integer $limit
   * @return array
   */
  public function search($needle, $consultationId, $limit=30) {
    $result = array();
    if($needle !== '' && !empty($consultationId) && is_int($limit)) {
      $select = $this->select();
      $select->from(
        array('inp'=>'inpt'),
        array('expl'=>'SUBSTRING(expl,1,100)', 'qi', 'tid', 'thes')
      );
      $select ->where("inp.thes LIKE '%$needle%' OR inp.expl LIKE '%$needle%'");
      $select ->where("inp.`block`!= 'y'");
      $select ->where("inp.`user_conf`='c'");
      // if no consultation is set, search in generell articles
      $select->where('inp.kid = ?', $consultationId);
      $select->limit($limit);
      
      $result = $this->fetchAll($select)->toArray();
      
    }
    return $result;
  }
  
  /**
   * Returns number of users who added at least one input to given consultation
   *
   * @param integer $kid
   * @throws Zend_Validate_Exception
   * @return integer
   */
  public function getCountParticipantsByConsultation($kid) {
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter kid must be integer!');
    }
    return $this->fetchAll(
        $this->select()
          ->distinct()
          ->from($this, array('uid'))
          ->where('kid = ?', $kid)
          ->where('uid > ?', 1)
      )->count();
  }
  
  /**
   * Liefert eine CSV-Liste aller abzustimmenen Beiträge einer Konsultation
   * @param inter $kid
   * @return array
   */
  public function getVotingchain($kid) {
    if(empty($kid)) {
      return array();
    }
    
    $select = $this->select();
    $select->from($this, array('tid'=>'tid', 'qi'=>'qi'));
    $select->where('kid=?', $kid);
    $select->where('vot=?', 'y');
    
    $rows = $this->fetchAll($select)->toArray();
    $tlist = array();
    $qlist = array();
    foreach($rows AS $inpt) {
      $tlist[]=$inpt['tid'];
      $qlist[]=$inpt['qi'];
    }
    $list = array(
      'tid'=>$tlist,
      'qi' =>$qlist
    );
    return $list;
  }
  
  public function getThesisbyQuestion($kid, $qid) {
    if(empty($kid) || empty($qid)) {
      return array();
    }
    
    $result = array();
    
    $select = $this->select();
    $select->where('kid=?', $kid);
    $select->where('qi=?', $qid);
    $select->where('vot=?', 'y');
    
    $rowSet = $this->fetchAll($select)->toArray();
    foreach($rowSet AS $row) {
      $result[$row['tid']] = $row;
    }
    
    return $result;
  }
  
  public function getThesisbyTag($kid, $tagId) {
    if(empty($kid) || empty($tagId)) {
      return array();
    }
    
    $result = array();
    $db = $this->getAdapter();
    $select = $db->select();
    $select->from(array('it' => 'inpt_tgs'));
    $select->joinLeft(array('i' => 'inpt'), 'i.tid = it.tid');
    $select->where('i.kid=?', $kid);
    $select->where('i.vot=?', 'y');
    $select->where('it.tg_nr = ?', (int)$tagId);

    $stmt = $db->query($select);
    $rowSet = $stmt->fetchAll();

    if($rowSet) {
      $result = $rowSet;
    }
    return $result;

  }
  
  /**
   * Migrate tags in csv-form from table inputs
   * to db-relation table inpt_tgs
   * DONT USE IN LIVE-SYSTEM
   */
  public function migrateTags() {
    $inputTagsModel = new Model_InputsTags();
    
    $select = $this->select();
    $rowset = $this->fetchAll($select)->toArray();
    
    foreach($rowset AS $input) {
      if(!empty($input['tg_nrs'])) {
        $tags = explode(',', $input['tg_nrs']);
        $inputTagsModel->insertByInputsId($input['tid'], $tags);
        echo($input['tid'] . ':' .$input['tg_nrs'] . '<br />');
      }
      
    }
  }
  
  /**
   * Returns voting theses by question
   *
   * @param integer $qid
   * @throws Zend_Validate_Exception
   * @return Zend_Db_Table_Rowset
   */
  public function getVotingthesesByQuestion($qid) {
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($qid)) {
      throw new Zend_Validate_Exception('Given parameter qid must be integer!');
    }
    
    return $this->fetchAll(
      $this->select()
        ->where('qi = ?', $qid)
        ->where('vot = ?', 'y')
    );
  }
  
  /**
   * set a new owner of written input by a user and consultation
   * @param integer $uid
   * @param integer $targetUid
   * @param integer $kid
   */
  public function transferInputs($uid, $targetUid, $kid) {
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($uid) || !$validator->isValid($targetUid) || !$validator->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter qid must be integer!');
    }
    $select = $this->select();
    $select->where('uid = ?', $uid);
    $select->where('kid = ?', $kid);
    
    $rowset = $this->fetchAll($select);
    foreach($rowset AS $input) {
      $input->uid = $targetUid;
      $input->save();
    }
    return true;
    
  }
}
