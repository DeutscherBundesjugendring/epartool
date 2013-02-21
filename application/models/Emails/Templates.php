<?php
/**
 * Users
 * @desc    Class of emails
 * @author  Jan Suchandt
 */
class Model_Emails_Templates extends Zend_Db_Table_Abstract {
  protected $_name = 'ml_def';
  protected $_primary = 'mid';
  
  protected $_flashMessenger = null;
  
  protected $_auth = null;
  
  public function init() {
    $this->_auth = Zend_Auth::getInstance();
    $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
  }
  
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
    return $row;
  }

  /**
   * add
   * @desc add new entry to db-table
   * @name add
   * @param array $data
   * @return integer primary key of inserted entry
   */
  public function add($data) {
    $row = $this->createRow($data);

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
    if (!$this->exists($id)) {
      return 0;
    }

    // where
    $where = $this->getDefaultAdapter()
        ->quoteInto($this->_primary[1] . '=?', $id);
    $result = $this->delete($where);
    return $result;
  }

  /**
   * exists
   * @desc check if a entitie exists
   * @param integer $id user-id
   * @return boolean
   */
  public function exists($id) {
    // is int?
    $validator = new Zend_Validate_Int();
    if (!$validator->isValid($id)) {
      return false;
    }
    // exists?
    if ($this->find($id)->count() < 1) {
      return false;
    }
    else {
      return true;
    }
  }

  
  /**
   * Return all
   */
  public function getAll() {
    return $this->fetchAll($this->select()->order('mid'));
  }
  
  /**
   * Return all
   */
  public function getAllReferences() {
    $select = $this
    ->select()
    ->distinct('refnm')
    ->from(
      array('tmpl'=>'ml_def'),
      array(
        'subj',
        'refnm'
      )
    )
    ->order('refnm');
    $return = array();
    $rows = $this->fetchAll($select)->toArray();
    foreach($rows AS $val) {
      $return[$val['refnm']] = $val['subj'];
    }
    return $return;
  }
  
  /**
   * getByName
   * @desc returns entry by id
   * @name getByName
   * @param string $name
   * @return tablerowset
   * @author JSU
   */
  public function getByName($name) {
    if (empty($name)) {
      return false;
    }
    // format
    $name = strip_tags(trim($name));
    
    $select = $this->select()
    ->where('refnm LIKE ?', $name)
    ->limit(1);
    $row = $this->fetchRow($select);
    return $row;
  }
  
}

