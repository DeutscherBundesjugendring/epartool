<?php
/**
 * Tags
 * @desc    Class of Tags,
 * @author  Jan Suchandt
 */
class Model_Tags extends Zend_Db_Table_Abstract {
  protected $_name = 'tgs';
  protected $_primary = 'tg_nr';
  
  protected $_dependentTables = array('Model_InputsTags');

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

    return $this->find($id)->current()->toArray();
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
    $row = $this->createRow();
    $row->setFromArray($data);
    
    return $row->save();
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
    $select->where('uid=?', $uid);
    $result = $this->fetchAll($select);
    return $result->toArray();
  }
  
  /**
   * Returns array of options for use in Zend_Form_Element_MultiCheckbox
   * i.e. array of all available Tags
   *
   * @return array
   */
  public function getAdminInputFormMulticheckboxOptions() {
    $options = array();
    $select = $this->select();
    $select->order('tg_de');
    $rowset = $this->fetchAll($select);
    foreach ($rowset as $row) {
      $options[$row->tg_nr] = $row->tg_de;
    }
    return $options;
  }
  
  /**
   * Returns array of tags used within given consultation incl. number of usage
   *
   * @param integer $kid
   * @throws Zend_Validate_Exception
   * @return array
   */
  public function getAllByConsultation($kid) {
    $return = array();
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given kid must be integer!');
    }
    
    // Fetch all tags
    $tags = $this->fetchAll();
    
    foreach ($tags as $tag) {
      $db = $this->getAdapter();
      $select = $db->select();
      
      // count number of assignments per tag and consultation over all inputs
      $select->from(array('it' => 'inpt_tgs'), array(new Zend_Db_Expr('COUNT(it.tg_nr) AS count')));
      $select->join(array('i' => 'inpt'), 'i.tid = it.tid', array());
      $select->where('i.kid = ?', $kid)->where('it.tg_nr = ?', $tag->tg_nr);
      $select->group('it.tg_nr');
      
      $stmt = $db->query($select);
      $result = $stmt->fetchAll();
      
      if (!empty($result)) {
        if ($result[0]['count'] > 0) {
          $return[$tag->tg_nr] = $tag->toArray();
          $return[$tag->tg_nr]['count'] = $result[0]['count'];
        }
      }
    }
    
    return $return;
  }
  
  /**
   * Returns all rows ordered by tg_de
   * @return Zend_Db_Table_Rowset
   */
  public function getAll() {
    return $this->fetchAll($this->select()->order('tg_de'));
  }
}
