<?php
/**
 * Questions
 * @desc    Class of questions, every consultation has questions (count n), users can write entries for every question
 * @author  Jan Suchandt
 */
class Model_Questions extends Zend_Db_Table_Abstract {
  protected $_name = 'quests';
  protected $_primary = 'qi';

  protected $_referenceMap = array(
    'Consultations' => array(
      'columns' => 'kid',
      'refTableClass' => 'Model_Consultations',
      'refColumns' => 'kid'
    ),
    'Inputs' => array(
      'columns' => 'qi',
      'refTableClass' => 'Model_Inputs',
      'refColumns' => 'qi'
    ),
  );

  /**
   * getById
   * @desc returns entry by id
   * @name getById
   * @param integer $id
   * @return array
   */
  public function getById($id) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return array();
    }

    $row = $this->find($id)->current();
//    $subRow = $row->findDependentRowset('Model_Inputs');
    
    $aQuestion = $row->toArray();
//    $aQuestion['inputs'] = $subRow->toArray();

    return $aQuestion;
  }

  /**
   * add
   * @desc add new entry to db-table
   * @name add
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
   * @name updateById
   * @param integer $id
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
   * getByConsultation
   * @desc returns entries by consultations-id
   * @name getByConsultation
   * @param integer $kid id of consultation
   * @return Zend_Db_Table_Rowset
   */
  public function getByConsultation($kid) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($kid)) {
      throw new Zend_Exception('Given kid must be integer!');
    }

    // fetch
    $select = $this->select();
    $select->where('kid=?', $kid);
    $select->order('nr');
    return $this->fetchAll($select);
  }
  
  public function getNext($qid) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($qid)) {
      throw new Zend_Exception('Given qid must be integer!');
    }
    
    $current = $this->find($qid)->current();
    
    // fetch
    $select = $this->select();
    $select->where('kid=?', $current->kid)->where('nr>?', $current->nr);
    $select->order('nr');
    $select->limit(1);
    return $this->fetchRow($select);
  }
  
  /**
   * Get max qi
   *
   * @return integer
   */
  public function getMaxId() {
    $row = $this->fetchAll(
            $this->select()
                ->from($this, array(new Zend_Db_Expr('max(qi) as maxId')))
            )->current();
    return $row->maxId;
  }
}

