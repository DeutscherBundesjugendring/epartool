<?php
/**
 * Entries
 * @desc    Class of Inputs, userentries to questions of a consultation
 * @author  Jan Suchandt
 */
class Model_Inputs extends Zend_Db_Table_Abstract {
  protected $_name = 'inpt';
  protected $_primary = 'tid';

  protected $_referenceMap = array(
    'Questions' => array(
      'columns' => 'qi', 'refTableClass' => 'Model_Questions', 'refColumns' => 'qi'
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
   * @param string $order [optional] MySQL Order Expression, e.g. 'votes DESC'
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
  public function getSelectByQuestion($qid, $order = 'tid ASC', $limit = null) {
    $intVal = new Zend_Validate_Int();
    $select = $this->select();
    $select->where('qi=?', $qid);
    if (is_string($order)) {
      $select->order($order);
    }
    if ($intVal->isValid($limit)) {
      $select->limit($limit);
    }
    
    return $select;
  }
}

