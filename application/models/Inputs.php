<?php
/**
 * Entries
 * @desc    Class of Inputs, userentries to questions of a consultation
 * @author  Jan Suchandt
 */
class Inputs extends Zend_Db_Table_Abstract {
  protected $_name = 'inpt';
  protected $_primary = 'tid';

  protected $_referenceMap = array(
    'Questions' => array(
      'columns' => 'qi', 'refTableClass' => 'Questions', 'refColumns' => 'qi'
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
    $subrow1 = $row->findTagsViaInputsTags()->toArray();

    $result = $row->toArray();
    $result['tags'] = $subrow1;

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
    $select->where('by=?', $uid);
    $result = $this->fetchAll($select);
    return $result->toArray();
  }

  /**
   * getByQuestion
   * @desc returns entry by question-id
   * @name getByQuestion
   * @param integer $qid id of question (qi in mysql-table)
   * @return array
   */
  public function getByQuestion($qid) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($qid)) {
      return array();
    }

    // fetch
    $select = $this->select();
    $select->where('qi=?', $qid);
    $result = $this->fetchAll($select);
    return $result->toArray();
  }
}

