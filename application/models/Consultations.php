<?php
/**
 * Consultations
 * @desc    Class of consultation
 * @author  Jan Suchandt
 */
class Consultations extends Zend_Db_Table_Abstract {
  protected $_name = 'cnslt';
  protected $_primary = 'kid';

  protected $_dependentTables = array(
    'Articles', 'Questions', 'Votes', 'Votes_Rights'
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
      $subrow1 = $row->findArticles()->toArray();
      // find Questions
      $subrow2 = $row->findQuestions()->toArray();
  
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
   * @return array
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
   * getVotingRights
   * @desc return the rights of
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
  
  public function getLastId() {
    $row = $this->fetchRow(
            $this->select()
                ->from($this, array(new Zend_Db_Expr('max(kid) as maxId'))));
    return $row->maxId;
  }
}

