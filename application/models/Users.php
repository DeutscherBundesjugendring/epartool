<?php
/**
 * Users
 * @desc    Class of user
 * @author  Jan Suchandt
 */
class Model_Users extends Zend_Db_Table_Abstract {
  protected $_name = 'users';
  protected $_primary = 'uid';

  protected $_dependentTables = array(
    'Model_Votes_Rights'
  );
  
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
    // Nachschauen, ob eingetragene E-Mail-Adresse schon existiert
    if (!$this->emailExists($data['email'])) {
      // E-Mail-Adresse existiert noch nicht
      $confirm_key = md5($data['email'] . mt_rand() . getenv('REMOTE_ADDR') . time());
      // Datensatz eintragen
      $insertData = array(
        'block' => 'u',
        'ip' => getenv('REMOTE_ADDR'),
        'agt' => getenv('HTTP_USER_AGENT'),
        'name' => $data['name'],
        'email' => $data['email'],
        'pwd' => md5($data['register_password']),
        'newsl_subscr' => $data['newsl_subscr'],
        'confirm_key' => $confirm_key,
      );
      // Nutzer in DB schreiben
      $id = $this->add($insertData);
      
      // Beiträge aus Session in DB schreiben
      $inputModel = new Model_Inputs();
      $inputModel->storeSessionInputsInDb($id);
      
      $this->sendRegisterConfirmationMail($id);
    } else {
      $this->_flashMessenger->addMessage('Die angegebene E-Mail-Adresse existiert schon!', 'error');
    }
    if (isset($id) && $id > 0) {
      return true;
    } else {
      return false;
    }
  }
  
  public function getAdmins() {
    $select = $this->select()->where('lvl = ?', 'adm')->order('name');
    return $this->fetchAll($select);
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
  
  protected function sendRegisterConfirmationMail($uid) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($uid)) {
      throw new Zend_Exception('Given uid must be integer!');
      return false;
    }
    $userRow = $this->find($uid)->current();
    if ($userRow->block == 'u' && !empty($userRow->confirm_key)) {
      $mailBody = 'Herzlich willkommen ' . $userRow->name . '!' . "\n\n"
        . 'Bitte bestätige deine Registrierung auf ' . Zend_Registry::get('httpHost') . ':' . "\n\n"
        . Zend_Registry::get('baseUrl') . '/user/registerconfirm/ckey/' . $userRow->confirm_key . "\n\n";
      if (APPLICATION_ENV == 'development') {
        $logger = Zend_Registry::get('log');
        $logger->debug('E-Mail: ' . $mailBody);
      } else {
        // E-Mail verschicken
        $mail = new Zend_Mail();
        $mail->setBodyText($mailBody);
        $mail->setFrom('somebody@example.com', 'DBJR - Strukturierter Dialog');
        $mail->addTo($identity->email, $identity->name);
        $mail->setSubject('DBJR: Beitragsbestätigung');
        $mail->send();
      }
      return true;
    }
    return false;
  }
  
  /**
   * Holt unbestätigte Beiträge aus der Datenbank und verschickt eine E-Mail
   * an den Nutzer, welche Links zur Bestätigung der Beiträge enthält
   *
   * @param integer|object $identity
   * @return boolean
   * @todo Text, Betreff, Absender anpassen
   */
  public function sendInputsConfirmationMail($identity) {
    $intVal = new Zend_Validate_Int();
    $userRow = null;
    if ($intVal->isValid($identity)) {
      $userRow = $this->find($identity)->current();
    } elseif (is_object($identity)) {
      $userRow = $identity;
    }
    if (!empty($userRow)) {
      // Hole alle nicht bestätigten Beiträge
      $inputModel = new Model_Inputs();
      $unconfirmedInputs = $inputModel->getUnconfirmedByUser($userRow->uid);
      if (!empty($unconfirmedInputs)) {
        $mailBody = 'Zu bestätigende Beiträge:' . "\n\n";
        foreach ($unconfirmedInputs as $input) {
          $mailBody.= '(Id: ' . $input->tid . '): ' . $input->thes . "\n"
            . 'Bitte diesen Link klicken oder diesen URL in die Adresszeile des Browsers kopieren, um den Beitrag zu bestätigen:' . "\n"
            . Zend_Registry::get('baseUrl') . '/input/mailconfirm/kid/' . $input->kid . '/ckey/'
            . $inputModel->generateConfirmationKey($input->tid) . "\n\n";
        }
        if (APPLICATION_ENV == 'development') {
          $logger = Zend_Registry::get('log');
          $logger->debug('E-Mail: ' . $mailBody);
        } else {
          // E-Mail verschicken
          $mail = new Zend_Mail();
          $mail->setBodyText($mailBody);
          $mail->setFrom('somebody@example.com', 'DBJR - Strukturierter Dialog');
          $mail->addTo($identity->email, $identity->name);
          $mail->setSubject('DBJR: Beitragsbestätigung');
          $mail->send();
        }
        return true;
      } else {
        // keine zu bestätigenden Beiträge
        return false;
      }
    } else {
      // keine Identität
      return false;
    }
  }
  
  public function confirmByCkey($ckey) {
    $return = false;
    $alnumVal = new Zend_Validate_Alnum();
    if (!$alnumVal->isValid($ckey)) {
      throw new Zend_Validate_Exception();
      return $return;
    }
    $select = $this->select();
    $select->where('confirm_key = ?', $ckey);
    $row = $this->fetchAll($select)->current();
    if (!empty($row)) {
      $return = true;
      $row->block = 'c';
      $row->confirm_key = '';
      $row->save();
      // Nutzer einloggen
      $authStorage = $this->_auth->getStorage();
      // die gesamte Tabellenzeile in der Session speichern,
      // wobei das Passwort unterdrückt wird
      $row->pwd = null;
      $row->setReadOnly(true);
      $authStorage->write($row);
      $this->_flashMessenger->addMessage('Du hast deine Registrierung bestätigt und bist nun eingeloggt!'
        . '<br/>Eine E-Mail zur Bestätigung deiner Beiträge wurde verschickt.', 'success');
    }
    return $return;
  }
  
  public function emailExists($email) {
    $select = $this->select();
    $select->from($this, array(new Zend_Db_Expr('COUNT(*) as count')));
    $select->where('email = ?', $email);
    $row = $this->fetchAll($select)->current();
    if ($row->count > 0) {
      return true;
    } else {
      return false;
    }
  }
}

