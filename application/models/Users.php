<?php
/**
 * Users
 * @desc    Class of user
 * @author  Jan Suchandt
 */
class Users extends Zend_Db_Table_Abstract {
  protected $_name = 'users';
  protected $_primary = 'uid';

  protected $_dependentTables = array(
    'Votes_Rights'
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
    $subrow1 = $row->findVotes_Rights()->toArray();

    $result = $row->toArray();
    $result['votingrights'] = $subrow1;
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
    if ($this->exists($id)) {
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
   * @desc check if a user exists
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
   * login
   * @desc try to login
   * @name login
   * @param string $name
   * @param string $password
   * @return integer
   *
   * @todo implement
   */
  public function login($name, $password) {
    return 0;
  }

  /**
   * register
   * @desc register user (insert entry) and send e-mail to user
   * @name register
   * @param array $data
   * @return integer uid of registered user
   *
   * @todo implement
   */
  public function register($data) {
    return 0;
  }

  /**
   * recoverPassword
   * @desc generate a new password and send e-mail to user
   * @name recoverPassword
   * @return string
   *
   * @todo implement
   */
  private function recoverPassword() {
    $newPassword = $this->generatePassword();
    return 0;
  }

  /**
   * generatePassword
   * @desc generate a password for user
   * @name generatePassword
   * @return string
   *
   * @todo implement
   */
  private function generatePassword() {
    return 0;
  }
}

