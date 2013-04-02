<?php
/**
 * Users
 * @desc    Class of user
 * @author  Jan Suchandt
 */
class Model_Users extends Model_DbjrBase {
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
   * register user (insert entry) and send e-mail to user
   *
   * @param array $data
   * @return boolean
   */
  public function register($data) {
    // check if email address exists already
    if (!$this->emailExists($data['email'])) {
      // email does not exist yet
      $confirm_key = md5($data['email'] . mt_rand() . getenv('REMOTE_ADDR') . time());
      // prepare insert record
      $insertData = array(
        'block' => 'u',
        'ip' => getenv('REMOTE_ADDR'),
        'agt' => getenv('HTTP_USER_AGENT'),
        'name' => $data['name'],
        'email' => $data['email'],
        'pwd' => md5($data['register_password']),
        'confirm_key' => $confirm_key,
        'group_type' => $data['group_type'],
        'age_group' => $data['age_group'],
        'regio_pax' => $data['regio_pax'],
        'cnslt_results' => $data['cnslt_results'],
        'newsl_subscr' => $data['newsl_subscr'],
      );
      // if group then also save group specifications
      if ($data['group_type'] == 'group' && isset($data['group_specs'])) {
        $insertData = array_merge($insertData, array(
          'source' => implode(',', $data['group_specs']['source']),
          'src_misc' => $data['group_specs']['src_misc'],
          'group_size' => $data['group_specs']['group_size'],
          'name_group' => $data['group_specs']['name_group'],
          'name_pers' => $data['group_specs']['name_pers'],
        ));
      }
      // write record to database
      $id = $this->add($insertData);
      
      // write inputs from session to database
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
  
  /**
   * Returns all Users that are Admins
   *
   * @return Zend_Db_Table_Rowset
   */
  public function getAdmins() {
    $select = $this->select()->where('lvl = ?', 'adm')->order('name');
    return $this->fetchAll($select);
  }

  /**
   * generate a new password and send e-mail to user
   *
   * @param string $email
   * @return boolean
   */
  public function recoverPassword($email) {
    $validator = new Zend_Validate_EmailAddress();
    if ($validator->isValid($email)) {
      if ($this->emailExists($email)) {
        $newPassword = $this->generatePassword();
        $row = $this->getByEmail($email);
        if ($row) {
          $row->pwd = md5($newPassword);
          $row->save();
          
          $toName = $row->name;
          $toEmail = $email;
          $subject = "Neues Passwort für den Strukturierten Dialog";
          $text = "Hallo {$row->name},\n"
            . "du hast ein neues Passwort angefordert.\n"
            . "Mit folgendem Passwort und deiner E-Mail-Adresse kannst du dich anmelden:\n"
            . "\n"
            . "Kennwort: $newPassword";
            
          $mailModel = new Model_Emails();
          
          // appropriate template has to be configured in database!
          $template = 'pwdrequest';
          $replace = array(
            '{{USER}}' => $row->name,
            '{{PWD}}' => $newPassword,
          );
          
          return $mailModel->send($toEmail, $subject, $text, 'pwdrequest', $replace);
        }
      } else {
        $this->_flashMessenger->addMessage('Kein Nutzer zur angegebenen E-Mail-Adresse vorhanden!', 'error');
      }
    }
    return false;
  }

  /**
   * generate a password for user
   * (function adopted from old system)
   *
   * @param integer $length
   * @return string
   */
  protected function generatePassword($length = 8) {
    $password="";
    // define possible characters - any character in this string can be
    // picked for use in the password, so if you want to put vowels back in
    // or add special characters such as exclamation marks, this is where
    // you should do it
    $possible = "2346789abcdfghjkmnpqrtvwxyzABCDEFGHJKLMNPQRTVWXYZ";

    // we refer to the length of $possible a few times, so let's grab it now
    $maxlength = strlen($possible);
  
    // check for length overflow and truncate if necessary
    if ($length > $maxlength) {
      $length = $maxlength;
    }
	
    // set up a counter for how many characters are in the password so far
    $i = 0;
    
    // add random characters to $password until $length is reached
    while ($i < $length) {
      // pick a random character from the possible ones
      $char = substr($possible, mt_rand(0, $maxlength-1), 1);
        
      // have we already used this character in $password?
      if (!strstr($password, $char)) {
        // no, so it's OK to add it onto the end of whatever we've already got...
        $password .= $char;
        // ... and increase the counter by one
        $i++;
      }
    }
    
    return $password;
  }
  
  /**
   * Sends E-Mail for Registration Confirmation
   *
   * @param integer $uid User ID
   * @throws Zend_Exception
   * @return boolean
   */
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
        . Zend_Registry::get('baseUrl')
        . Zend_Controller_Front::getInstance()->getBaseUrl()
        . '/user/registerconfirm/ckey/' . $userRow->confirm_key . "\n\n";
      
      $mailObj = new Model_Emails();
      
      return $mailObj->send($userRow->email,
        'Strukturierter Dialog: Registrierungsbestätigung', $mailBody);
    }
    return false;
  }
  
  /**
   * Holt unbestätigte Beiträge aus der Datenbank und verschickt eine E-Mail
   * an den Nutzer, welche Links zur Bestätigung der Beiträge enthält
   *
   * @param integer|object $identity
   * @return boolean
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
        // appropriate template has to be configured in database!
        $template = 'inpt_conf';
        $replace = array(
          '{{USER}}' => $userRow->name
        );
        
        $mailBody = 'Hallo ' . $userRow->name . "\n\n"
          . 'Bitte bestätige folgende Beiträge:' . "\n\n";
        foreach ($unconfirmedInputs as $input) {
          $inputText = '(Id: ' . $input->tid . '): ' . $input->thes . "\n"
            . 'Bitte diesen Link klicken oder diesen URL in die Adresszeile des Browsers kopieren, um den Beitrag zu bestätigen:' . "\n"
            . Zend_Registry::get('baseUrl')
            . Zend_Controller_Front::getInstance()->getBaseUrl()
            . '/input/mailconfirm/kid/' . $input->kid . '/ckey/'
            . $inputModel->generateConfirmationKey($input->tid) . "\n\n";
          $mailBody.= $inputText;
          $replace['{{USER_INPUTS}}'].= $inputText;
        }
        
        $mailObj = new Model_Emails();
        
        return $mailObj->send($identity->email, 'Strukturierter Dialog: Beitragsbestätigung', $mailBody, $template, $replace);
      } else {
        // keine zu bestätigenden Beiträge
        return false;
      }
    } else {
      // keine Identität
      return false;
    }
  }
  
  /**
   * Processes Registration Confirmation
   *
   * @param string $ckey
   * @throws Zend_Validate_Exception
   * @return boolean
   */
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
      // die gesamte Tabellenzeile in der Session speichern
      $row->pwd = null;
      $row->setReadOnly(true);
      $authStorage->write($row);
      $this->_flashMessenger->addMessage('Du hast deine Registrierung bestätigt und bist nun eingeloggt!'
        . '<br/>Eine E-Mail zur Bestätigung deiner Beiträge wurde verschickt.', 'success');
    }
    return $return;
  }
  
  /**
   * Checks if given email address already exists in database
   *
   * @param string $email
   * @return boolean
   */
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
  
  /**
   * Updates value of last activity with current timestamp
   *
   * @param integer $uid
   * @throws Zend_Exception
   */
  public function ping($uid) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($uid)) {
      throw new Zend_Exception('Given uid must be integer!');
    }
    $data = array('last_act' => new Zend_Db_Expr('NOW()'));
    $this->updateById($uid, $data);
  }
  
  /**
   * Returns all participants of given consultation
   *
   * @param integer $kid Consultation ID
   * @param string|array $order [optional] order by spec, Defaults to array('u.name', 'u.uid')
   * @return array
   */
  public function getParticipantsByConsultation($kid, $order = '') {
    if (empty($order)) {
      $order = array('u.name', 'u.uid');
    }
    $db = $this->getAdapter();
    $select = $db->select();
    $select->distinct()->from(array('u' => $this->_name));
    $select->joinInner(array('i' => 'inpt'), 'u.uid = i.uid', array());
    $select->where('i.kid = ?', $kid);
    $select->order($order);
    $stmt = $db->query($select);
    
    return $stmt->fetchAll();
  }
  
  /**
   * Return all users
   */
  public function getAll() {
    return $this->fetchAll($this->select()->order('name'));
  }
  
  public function getByEmail($email) {
    $validator = new Zend_Validate_EmailAddress();
    if ($validator->isValid($email)) {
      $select = $this->select();
      $select->where('email = ?', $email);
      return $this->fetchAll($select)->current();
    }
    return null;
  }
}

