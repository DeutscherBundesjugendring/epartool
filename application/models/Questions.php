<?php
/**
 * Questions
 * @desc    Class of questions, every consultation has questions (count n), users can write entries for every question
 * @author  Jan Suchandt
 */
class Questions extends Zend_Db_Table_Abstract {
  protected $_name = 'quests';
  protected $_primary = 'qi';

  protected $_referenceMap = array(
    'Consultations' => array(
      'columns' => 'kid',
      'refTableClass' => 'Consultations',
      'refColumns' => 'kid'
    )
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
    $result = $row->toArray();

    return $result;
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
   * @desc returns entry by consultations-id
   * @name getByConsultation
   * @param integer $kid id of consultation
   * @return array
   */
  public function getByConsultation($kid) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($kid)) {
      return array();
    }

    // fetch
    $select = $this->select();
    $select->where('kid=?', $kid);
    $result = $this->fetchAll($select);
    return $result->toArray();
  }
  
  public function getMaxId() {
    $row = $this->fetchAll(
            $this->select()
                ->from($this, array(new Zend_Db_Expr('max(qi) as maxId')))
            )->current();
    return $row->maxId;
  }
}

