<?php
/**
 * Articles
 * @desc    Class of articles
 * @author  Jan Suchandt
 */
class Model_Articles extends Zend_Db_Table_Abstract {
  protected $_name = 'articles';
  protected $_primary = 'art_id';

  protected $_referenceMap = array(
    'Consultations' => array(
      'columns' => 'kid',
      'refTableClass' => 'Model_Consultations',
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
    $result = array();
    $row = $this->find($id)->current();
    if ($row) {
      $result = $row->toArray();
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
   * Get all Articles that are not assigned to any Consultation, i.e. general Articles
   * Liefert alle Artikel, die zu keiner Konsultation gehören, d.h. allg. Artikel
   * @param string $orderBy [optional] Fieldname
   *
   * @return Zend_Db_Table_Rowset
   */
  public function getAllWithoutConsultation($orderBy = 'art_id') {
    return $this->getByConsultation(0, $orderBy);
  }
  
  /**
   * Get all Articles of a consultation
   *
   * @param integer $kid Id of consultation
   * @param string $orderBy [optional] Fieldname
   * @return Zend_Db_Table_Rowset or false
   */
  public function getByConsultation($kid = null, $orderBy = 'art_id') {
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($kid)) {
      throw new Zend_Exception('Given kid must be integer!');
      return false;
    }
    $select = $this->select()->where('kid = ?', $kid)->order($orderBy);
    return $this->fetchAll($select);
  }
  
  /**
   * Returns article by given RefName, e.g. 'about', 'imprint' etc.
   * used for static pages
   *
   * @param string $ref
   * @return array
   */
  public function getByRefName($ref) {
    $result = array();
    $select = $this->select();
    $select->where('ref_nm = ?', $ref);
    $row = $this->fetchAll($select)->current();
    if (!empty($row)) {
      $result = $row->toArray();
    }
    return $result;
  }
}

