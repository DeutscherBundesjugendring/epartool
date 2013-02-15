<?php
/**
 * Entries
 * @desc    Class of Inputs, userentries to questions of a consultation
 * @author  Jan Suchandt
 */
class Model_Inputs extends Zend_Db_Table_Abstract {
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
   * @return array
   */
  public function getByQuestion($qid, $order = 'tid ASC', $limit = null) {
    // is int?
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($qid)) {
      return array();
    }

    // fetch
    $select = $this->getSelectByQuestion($qid, $order, $limit);
    
    $result = $this->fetchAll($select);
    return $result->toArray();
  }
  
  /**
   * Returns number of inputs for a consultation
   *
   * @param integer $kid
   * @return integer
   */
  public function getCountByConsultation($kid) {
    $row = $this->fetchAll(
            $this->select()
              ->from($this, array(new Zend_Db_Expr('COUNT(*) as count')))
              ->where('kid = ?', $kid)
            )->current();
    return $row->count;
  }
  
	/**
   * Returns number of inputs for a question
   *
   * @param integer $qid
   * @return integer
   */
  public function getCountByQuestion($qid) {
    $row = $this->fetchAll(
            $this->select()
              ->from($this, array(new Zend_Db_Expr('COUNT(*) as count')))
              ->where('qi = ?', $qid)
              // nur nicht geblockte:
              ->where('block<>?', 'y')
              // nur bestätigte:
              ->where('user_conf=?', 'c')
            )->current();
    return $row->count;
  }
  
  /**
   * Returns Zend_Db_Table_Select for use in e.g. Paginator
   *
   * @param integer $qid
   * @param string $order
   * @param integer $limit
   */
  public function getSelectByQuestion($qid, $order = 'tid DESC', $limit = null) {
    $intVal = new Zend_Validate_Int();
    $select = $this->select();
    $select->where('qi=?', $qid)->where('block<>?', 'y')->where('user_conf=?', 'c');
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
}
