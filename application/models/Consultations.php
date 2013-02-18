<?php
/**
 * Consultations
 * @desc    Class of consultation
 * @author  Jan Suchandt
 */
class Model_Consultations extends Zend_Db_Table_Abstract {
  protected $_name = 'cnslt';
  protected $_primary = 'kid';

  protected $_dependentTables = array(
    'Model_Articles', 'Model_Questions', 'Model_Votes', 'Model_Votes_Rights'
  );

  /**
   * getById
   * @desc returns entry by id
   * @param integer $id consultations-id
   * @return array
   */
  public function getById($id) {
    $result = array();
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return array();
    }

    // find current consultation
    $row = $this->find($id)->current();
    if (!empty($row)) {
      // find Articles
      $subrow1 = $row->findModel_Articles()->toArray();
      // find Questions
      $subrow2 = $row->findModel_Questions()->toArray();
  
      $result = $row->toArray();
      $result['articles'] = $subrow1;
      $result['questions'] = $subrow2;
    }

    return $result;
  }

  /**
   * add
   * @desc add new entry to db-table
   * @param array $data
   * @return integer primary key of inserted entry
   *
   * @todo add validators for table-specific data (e.g. date-validator)
   */
  public function add($data) {

    return (int)$this->insert($data);
  }

  /**
   * updateById
   * @desc update entry by id
   * @param integer $id consultations-id
   * @param array $data
   * @return integer
   *
   * @todo add validators for table-specific data (e.g. date-validator)
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

    $where = $this->getDefaultAdapter()
        ->quoteInto($this->_primary[1] . '=?', $id);
    return $this->update($data, $where);
  }

  /**
   * deleteById
   * @desc delete entry by id
   * @param integer $id consultations-id
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
   * getLast
   * @desc returns the last consultations by sort
   * @param integer $limit count of consultations
   * @return Zend_Db_Table_Rowset_Abstract
   * @todo check if all conditions which needed (e.g. expire dates => show expired consultations?) are implemented
   */
  public function getLast($limit = 3) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($limit)) {
      return array();
    }

    // fetch
    $where = array(
      'public="y"'
    );
    $order = array(
      'ord DESC'
    );
    $result = $this->fetchAll($where, $order, $limit);
    return $result;
  }

  /**
   * getVotingRights
   * @desc return the rights of
   * @param integer $id consultations-id
   * @return array
   */
  public function getVotingRights($id) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return array();
    }
    // find current consultation
    $row = $this->find($id)->current();
    // find Voting-Rights (see model Votes/Rights.php)
    $subrow1 = $row->findVotes_Rights()->toArray();

    return $subrow1;
  }

  /**
   * getVotingResults
   * @desc return the results of
   * @param integer $id consultations-id
   * @return array
   */
  public function getVotingResults($id) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return array();
    }
    // find current consultation
    $row = $this->find($id)->current();
    // find Voting-Rights (see model Votes/Rights.php)
    $subrow1 = $row->findVotes()->toArray();

    return $subrow1;
  }
  
  /**
   * Returns highest ID from database
   * @return integer
   */
  public function getLastId() {
    $row = $this->fetchRow(
            $this->select()
                ->from($this, array(new Zend_Db_Expr('max(kid) as maxId'))));
    return $row->maxId;
  }
  
  /**
   * Returns array of entries for use as pages in Zend_Navigation
   * @return array
   */
  public function getNavigationEntries() {
    $entries = array();
    $select = $this->select();
    $rowSet = $this->fetchAll($select);
    foreach ($rowSet as $row) {
      $entries[] = array(
        'label' => $row->titl,
        'module' => 'admin',
        'action' => 'index',
        'controller' => 'consultation',
        'params' => array (
          'kid' => $row->kid
        ),
      );
    }
    return $entries;
  }
  
  /**
   * Returns all rows with public = 'y'
   * @return Zend_Db_Table_Rowset
   */
  public function getPublic() {
    $select = $this->select()->where('public = ?', 'y')->order('ord DESC');
    return $this->fetchAll($select);
  }
  
  /**
   * Returns entries for the teaser view helper
   *
   * @return array
   */
  public function getTeaserEntries() {
    $entries = array();
    
    $select = $this->select();
    $select->where('public = ?', 'y');
    $select->order(array(
      'ord DESC',
    ));
    $rowSet = $this->fetchAll($select);
    
    $date = new Zend_Date();
    foreach ($rowSet as $row) {
      // Berechne die Zeitabstände der einzelnen Datumsfelder zum aktuellen Zeitpunkt
      $timeDiff = array(
        'inp_fr' => Zend_Date::now()->sub($date->set($row->inp_fr))->toValue(),
        'inp_to' => Zend_Date::now()->sub($date->set($row->inp_to))->toValue(),
        'vot_fr' => Zend_Date::now()->sub($date->set($row->vot_fr))->toValue(),
        'vot_to' => Zend_Date::now()->sub($date->set($row->vot_to))->toValue(),
      );
      $relevantField = 'inp_fr';
      foreach ($timeDiff as $field => $value) {
        if ($value > 0) {
          // relevantes Datumsfeld darf nicht in der Zukunft liegen
          if ($value < $timeDiff[$relevantField]) {
            // relevantes Feld ist dasjenige mit dem kleinsten positiven Abstand
            $relevantField = $field;
          }
        }
      }
      if ($timeDiff[$relevantField] < 0) {
        // wenn relevantes Feld in Zukunft liegt, fällt der Datensatz raus
        continue;
      }
      // Datensatz im entries Array ablegen, key ist der Rang
      $entries[$row->ord] = $row->toArray();
      $entries[$row->ord]['relevantField'] = $relevantField;
    }
    
    // Sortiere nach Rang in absteigender Reihenfolge
    krsort($entries);
    // Nur die ersten drei Einträge werden benötigt
    $entries = array_slice($entries, 0, 3);
    
    return $entries;
  }
  
  /**
   * Get all consultations where given user has participated in
   *
   * @param integer $uid
   */
  public function getByUser($uid) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($uid)) {
      throw new Zend_Validate_Exception('Given uid must be integer!');
    }
    
    $db = $this->getAdapter();
    $select = $db->select();
    $select
      ->from(array('i' => 'inpt'), 'i.kid')
      ->distinct()
      ->where('i.uid = ?', $uid);
    $stmt = $db->query($select);
    $rowSet = $stmt->fetchAll();
    $kidArray = array();
    foreach ($rowSet as $row) {
      $kidArray[] = $row['kid'];
    }
    
    $select2 = $this->select()
      ->where('kid IN (?)', $kidArray)
      ->order('ord DESC');
      
    return $this->fetchAll($select2);
  }
  
  /**
   * Return all
   */
  public function getAll() {
    return $this->fetchAll($this->select()->order('ord DESC'));
  }
  
  /**
   * Search for inputs, questions and articles of consultations
   */
  public function search($needle) {
    $result = array();
    
    if($needle==='') {
      return $result;
    }
    
    $select = $this->select();
    $select->from(
        array('c'=>'cnslt'),
        array(
          'cid'=>'kid',
          'titel'=>'titl'
        )
      );
    $select->where('proj="sd"');
    $select->order('ord DESC');
    $rows = $this->fetchAll($select);
    $i = 0;
    foreach($rows AS $consultation) {
      $result[$i] = $consultation->toArray();
      // search articles
      $articles = new Model_Articles();
      $result[$i]['articles'] = $articles->search($needle, $consultation->cid);
      // search questions
      $questions = new Model_Questions();
      $result[$i]['questions'] = $questions->search($needle, $consultation->cid);
      // search questions
      $inputs = new Model_Inputs();
      $result[$i]['inputs'] = $inputs->search($needle, $consultation->cid);
      $i++;
    }
    
    return $result;
  }
}

