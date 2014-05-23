<?php

class Model_Users extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'users';
    protected $_primary = 'uid';
    protected $_dependentTables = array(
            'Model_User_Info',
            'Model_Votes_Rights'
    );

    protected $_flashMessenger = null;
    protected $_auth = null;

    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();
        $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
    }

    /**
     * Returns user by id
     * @param  integer $id
     * @return array
     */
    public function getById($id)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return array();
        }

        $row = $this->find($id)->current();

        return $row;
    }

    /**
     * Creates a new user
     * @param  array   $data
     * @return integer        Primary key of the inserted entry
     */
    public function add($data)
    {
        $row = $this->createRow($data);

        return $row->save();
    }

    /**
     * Updates user by id
     * @param  integer $id
     * @param  array   $data
     * @return integer
     */
    public function updateById($id, $data)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return 0;
        }

        if ($this->find($id)->count() < 1) {
            return 0;
        }

        $where = $this->getDefaultAdapter()->quoteInto($this->_primary[1] . '=?', $id);

        return $this->update($data, $where);
    }

    /**
     * Deletes user by id
     * @param  integer $id
     * @return integer
     */
    public function deleteById($id)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return 0;
        }

        if (!$this->exists($id)) {
            return 0;
        }

        $where = $this->getDefaultAdapter()->quoteInto($this->_primary[1] . '=?', $id);
        $result = $this->delete($where);

        return $result;
    }

    /**
     * Checks if a user exists
     * @param  integer $id user-id
     * @return boolean
     */
    public function exists($id)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return false;
        }

        if ($this->find($id)->count() < 1) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * register user (insert entry) and send e-mail to user
     * if already registered only add new record to user info table
     *
     * @param  array $data
     * @return array ('uid' => <uid>, 'newlyRegistered' => boolean)
     */
    public function register($data)
    {
        $kid = 0;
        if (isset($data['kid'])) {
            $kid = $data['kid'];
        }
        $userInfoModel = new Model_User_Info();
        $newlyRegistered = false;
        $password = '';

        if (!$this->emailExists($data['email'])) {
            $passConf = Zend_Registry::get('systemconfig')->security->password;
            $password = $this->getRandString($passConf->length, $passConf->allowedChars);

            $insertData = array(
                'block' => 'u',
                'ip' => getenv('REMOTE_ADDR'),
                'agt' => getenv('HTTP_USER_AGENT'),
                'name' => $data['name'],
                'email' => $data['email'],
                'pwd' => $this->hashPassword($password),
                'confirm_key' => '', // confirm key not needed, user will be confirmed when he confirms his inputs
                'group_type' => $data['group_type'],
                'age_group' => $data['age_group'],
                'regio_pax' => $data['regio_pax'],
                'cnslt_results' => $data['cnslt_results'],
                'newsl_subscr' => $data['newsl_subscr'],
            );
            $id = $this->add($insertData);
            $newlyRegistered = true;
        } else {
            $userRow = $this->getByEmail($data['email']);
            $id = $userRow->uid;
        }

        $insertDataUserInfo = array(
            'uid' => $id,
            'kid' => $kid,
            'ip' => getenv('REMOTE_ADDR'),
            'agt' => getenv('HTTP_USER_AGENT'),
            'name' => $data['name'],
            'group_type' => $data['group_type'],
            'age_group' => $data['age_group'],
            'regio_pax' => $data['regio_pax'],
            'cnslt_results' => $data['cnslt_results'],
            'newsl_subscr' => $data['newsl_subscr'],
            'date_added' => new Zend_Db_Expr('NOW()'),
            'cmnt_ext' => $data['cmnt_ext'],
        );

        // if group then also save group specifications
        if ($data['group_type'] == 'group' && isset($data['group_specs'])) {
            $insertDataUserInfo = array_merge(
                $insertDataUserInfo,
                array(
                    'source' => implode(',', $data['group_specs']['source']),
                    'src_misc' => $data['group_specs']['src_misc'],
                    'group_size' => $data['group_specs']['group_size'],
                    'name_group' => $data['group_specs']['name_group'],
                    'name_pers' => $data['group_specs']['name_pers'],
                )
            );
        } else {
            $insertDataUserInfo = array_merge(
                $insertDataUserInfo,
                array('group_size' => 1)
            );
        }

        $rowUserInfo = $userInfoModel->createRow($insertDataUserInfo);
        $userInfoId = $rowUserInfo->save();

        // write inputs from session to database
        $inputModel = new Model_Inputs();
        $inputModel->storeSessionInputsInDb($id);

        // register time for last activity
        $this->ping($id);

        return array(
            'uid' => $id,
            'newlyRegistered' => $newlyRegistered,
            'password' => $password,
        );
    }

    /**
     * Returns all Users that are Admins
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getAdmins()
    {
        $select = $this->select()->where('lvl = ?', 'adm')->order('name');

        return $this->fetchAll($select);
    }

    /**
     * Generates a new password and send e-mail to user
     * @param  string  $email
     * @return boolean
     */
    public function recoverPassword($email)
    {
        $validator = new Zend_Validate_EmailAddress();
        if ($validator->isValid($email)) {
            if ($this->emailExists($email)) {
                $passConf = Zend_Registry::get('systemconfig')->security->password;
                $newPassword = $this->getRandString($passConf->length, $passConf->allowedChars);
                $row = $this->getByEmail($email);
                if ($row) {
                    $row->password = $this->hashPassword($newPassword);
                    $row->save();

                    $mailer = new Dbjr_Mail();
                    $mailer
                        ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_FORGOTTEN_PASSWORD)
                        ->setPlaceholders(
                            array(
                                'to_name' => $row->name ? $row->name : $row->email,
                                'to_email' => $row->email,
                                'password' => $newPassword,
                            )
                        )
                        ->addTo($row->email)
                        ->send();

                    return true;
                }
            } else {
                $this->_flashMessenger->addMessage('Kein Nutzer zur angegebenen E-Mail-Adresse vorhanden!', 'error');
            }
        }

        return false;
    }

    /**
     * Hashes the password
     * @param  string $password The password to be hashed
     * @return string           The password hashed by blowfish
     */
    public function hashPassword($password)
    {
        $passConf = Zend_Registry::get('systemconfig')->security->password;
        $saltChars = implode('', array_merge(range(0, 9), range('a', 'z'), range('A', 'Z')));
        $saltBase = $passConf->globalSalt . floor(microtime(true)) . $this->getRandString(22, $saltChars);
        $salt = '$2a$' . $passConf->costParam . '$' . substr($saltBase, 0, 22);

        return crypt($password, $salt);
    }

    /**
     * Generates a pseudo random string
     * @param  integer $length  The length of the string.
     * @param  string  $chars   A string consisting of all characters that can be used in the string.
     *                          Defaults to printabale ASCII characters (32-127)
     * @return string           The pseudo random string
     */
    protected function getRandString($length, $chars = null)
    {
        $randString = '';
        if (!$chars) {
            $chars = '';
            for ($i = 32; $i <= 127; $i++) {
                $chars .= chr($i);
            }
        }

        $charCount = mb_strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $randString .= substr($chars, mt_rand(0, $charCount - 1), 1);
        }

        return $randString;
    }

    /**
     * @param  integer|object $identity
     * @param  integer        $kid
     * @return boolean
     */
    public function sendInputsConfirmationMail($identity, $kid)
    {
        $intVal = new Zend_Validate_Int();
        $userRow = null;
        if ($intVal->isValid($identity)) {
            $userRow = $this->find($identity)->current();
        } elseif (is_object($identity)) {
            $userRow = $identity;
        }
        if ($userRow) {
            $consultationModel = new Model_Consultations();
            $consultation = $consultationModel->getById($kid);

            $inputModel = new Model_Inputs();
            $unconfirmedInputs = $inputModel->getUnconfirmedByUser($userRow->uid, $kid, false);

            if (count($unconfirmedInputs) > 0) {

                $inputIds = array();
                $inputsText = '';
                $inputsHtml = '';
                foreach ($unconfirmedInputs as $input) {
                    $inputIds[] = $input->tid;
                    $inputsText .= $input->thes . "\n\n";
                    $inputsHtml .= '<p>' . $input->thes . '</p>';
                }
                $ckey = $inputModel->generateConfirmationKeyBulk($inputIds);
                $date = new Zend_Date();
                $baseUrl = Zend_Registry::get('baseUrl');

                $mailer = new Dbjr_Mail();
                $mailer
                    ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_INPUT_CONFIRMATION)
                    ->setPlaceholders(
                        array(
                            'to_name' => $userRow->name ? $userRow->name : $userRow->email,
                            'to_email' => $userRow->email,
                            'confirmation_url' =>  $baseUrl . '/input/mailconfirm/kid/' . $kid . '/ckey/' . $ckey,
                            'rejection_url' => $baseUrl . '/input/mailreject/kid/' . $kid . '/ckey/' . $ckey,
                            'consultation_title_long' => $consultation ? $consultation['titl'] : '',
                            'consultation_title_short' => $consultation ? $consultation['titl_short'] : '',
                            'input_phase_end' => $consultation ? $date->set($consultation['inp_to'])->get(Zend_Date::DATE_MEDIUM) : '',
                            'input_phase_start' => $consultation ? $date->set($consultation['inp_fr'])->get(Zend_Date::DATE_MEDIUM) : '',
                            'inputs_html' => $inputsHtml,
                            'inputs_text' => $inputsText,
                        )
                    )
                    ->addTo($userRow->email)
                    ->send();

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Processes Registration Confirmation
     * @deprecated User Confirmation now is done within Inputs Confirmation
     * @see Model_Inputs::confirmByCkey()
     *
     * @param  string                  $ckey
     * @throws Zend_Validate_Exception
     * @return boolean
     */
    public function confirmByCkey($ckey)
    {
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
            $this->_flashMessenger->addMessage(
                'Du hast deine Registrierung bestätigt und bist nun eingeloggt!<br/>Eine E-Mail zur Bestätigung deiner Beiträge wurde verschickt.',
                'success'
            );
        }

        return $return;
    }

    /**
     * Checks if given email address already exists in database
     * @param  string  $email
     * @return boolean
     */
    public function emailExists($email)
    {
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
     * @param  integer        $uid
     * @throws Zend_Exception
     */
    public function ping($uid)
    {
        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($uid)) {
            throw new Zend_Exception('Given uid must be integer!');
        }
        $data = array('last_act' => new Zend_Db_Expr('NOW()'));
        $this->updateById($uid, $data);
    }

    /**
     * Returns all participants of given consultation.
     * Participant is a user that has contributed to a given consultation
     * @param  integer      $kid   Consultation ID
     * @param  string|array $order Order by spec, Defaults to array('u.name', 'u.uid')
     * @return array
     */
    public function getParticipantsByConsultation($kid, $order = '')
    {
        if (empty($order)) {
            $order = array('u.name', 'u.uid');
        }

        $participants = $this->getAdapter()
            ->select()
            ->distinct()
            ->from(array('u' => $this->_name))
            ->joinInner(array('i' => 'inpt'), 'u.uid = i.uid', array())
            ->where('i.kid = ?', $kid)
            ->order($order)
            ->query()
            ->fetchAll();

        return $participants;
    }

    /**
     * Return all users
     */
    public function getAll()
    {
        return $this->fetchAll($this->select()->order('name'));
    }

    public function getByEmail($email)
    {
        $validator = new Zend_Validate_EmailAddress();
        if ($validator->isValid($email)) {
            $select = $this->select();
            $select->where('email = ?', $email);

            return $this->fetchAll($select)->current();
        }

        return null;
    }

    /**
     * Return all users which are confirmed
     */
    public function getAllConfirmed()
    {
        $select = $this->select();
        $select->where("block ='c'")->order('email');

        return $this->fetchAll($select)->toArray();
    }
}
