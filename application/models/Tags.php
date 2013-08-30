<?php
/**
 * Tags
 * @desc    Class of Tags,
 * @author  Jan Suchandt
 */
class Model_Tags extends Model_DbjrBase {
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
    $select->order('tg_de');
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
   * @param string $vot 'y' for inputs that confirmed for voting
   * @throws Zend_Validate_Exception
   * @return array
   */
  public function getAllByConsultation($kid, $vot='', $order='tg_de') {
    $return = array();
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given kid must be integer!');
    }
    
    // Get number of all inputs of this consultation
    $inputsModel = new Model_Inputs();
    $nrInputs = $inputsModel->getCountByConsultation($kid);
    
    // Fetch all tags
    $select = $this->select();
    // sort by $order
    if (!empty($order)) {
      $select->order($order);
    }
    $tags = $this->fetchAll($select);
    
    foreach ($tags as $tag) {
      $db = $this->getAdapter();
      $select = $db->select();
      
      // count number of assignments per tag and consultation over all inputs
      $select->from(array('it' => 'inpt_tgs'), array(new Zend_Db_Expr('COUNT(it.tg_nr) AS count')));
      $select->joinLeft(array('i' => 'inpt'), 'i.tid = it.tid', array());
      $select->where('i.kid = ?', $kid)
        ->where('it.tg_nr = ?', $tag->tg_nr);
      if(!empty($vot)) {
        $select->where('i.vot = ?', $vot);
      }
      $select->group('it.tg_nr');
      
      $stmt = $db->query($select);
      $result = $stmt->fetchAll();
      
      if (!empty($result)) {
        if ($result[0]['count'] > 0) {
          $return[$tag->tg_nr] = $tag->toArray();
          $return[$tag->tg_nr]['count'] = $result[0]['count'];
          $weight = 100*$result[0]['count']/$nrInputs;
          if ($weight < 33) {
            $return[$tag->tg_nr]['frequency'] = 'rare';
          } elseif ($weight >= 33 && $weight < 66) {
            $return[$tag->tg_nr]['frequency'] = 'medium';
          } elseif ($weight >= 66) {
            $return[$tag->tg_nr]['frequency'] = 'frequent';
          }
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
  
  /**
   * returns name by id
   * @param integer $id
   * @return string
   */
  public function getNameById($id) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return array();
    }

    $row = $this->find($id)->current();
    if($row) {
      return $row->tg_de;
    }
    else {
      return '';
    }
    

  }
}
