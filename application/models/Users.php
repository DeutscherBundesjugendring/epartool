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
     * Deletes user by uid. All users inputs are made anonymous.
     * @param  integer  $uid The user identifier
     * @return integer       Number of deleted rows, that is ether 1 or 0
     */
    public function deleteById($uid)
    {
        (new Model_Inputs())->update(['uid' => null], ['uid=?' => $uid]);
        (new Model_User_Info())->delete(['uid=?' => $uid]);

        return $this->delete(['uid=?' => $uid ]);
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
     * Registers new user
     * If user is already registered only the consultation specific data are updated
     * @param  array    $data   User data
     * @param  string   $string ConfirmKey for the given session
     * @return array            Info about the user [(int) user_id, (boolean) is_user_new]
     */
    public function register($data, $confirmKey = null)
    {
        if (!$this->emailExists($data['email'])) {
            $data['uid'] = $this->add(['block' => 'u', 'email' => $data['email']]);
            $isNew = true;
        } else {
            $data['uid'] = $this
                ->fetchRow(
                    $this
                        ->select()
                        ->from($this, ['uid'])
                        ->where('email=?', $data['email'])
                )
                ->uid;
            $isNew = false;
        }

        if (isset($data['kid'])) {
            $this->addConsultationData($data, $confirmKey);
        }

        return [$data['uid'], $isNew];
    }

    /**
     * Creates a new row in user_info data table
     * @param  array   $data The user supplied data to be inserted
     * @return integer       The user_info id
     */
    public function addConsultationData($data, $confirmKey = null)
    {
        $userConsultData = [
            'uid' => $data['uid'],
            'name' => $data['name'],
            'age_group' => $data['age_group'],
            'newsl_subscr' => $data['newsl_subscr'],
            'regio_pax' => $data['regio_pax'],
            'cnslt_results' => $data['cnslt_results'],
            'kid' => $data['kid'],
            'date_added' => new Zend_Db_Expr('NOW()'),
            'cmnt_ext' => $data['cmnt_ext'],
            'confirmation_key' => $this->_auth->hasIdentity() ? null : $confirmKey,
            'time_user_confirmed' => new Zend_Db_Expr('NOW()'),
            'is_contrib_under_cc' => $data['is_contrib_under_cc'],
        ];

        // if group then also save group specifications
        if (isset($data['group_specs'])) {
            $userConsultData = array_merge(
                $userConsultData,
                [
                    'source' => is_array($data['group_specs']['source']) ? implode(',', $data['group_specs']['source']) : null,
                    'src_misc' => $data['group_specs']['src_misc'],
                    'group_size' => $data['group_specs']['group_size'],
                    'name_group' => $data['group_specs']['name_group'],
                    'name_pers' => $data['group_specs']['name_pers'],
                ]
            );
        } else {
            $userConsultData = array_merge($userConsultData, ['group_size' => 1]);
        }

        return (new Model_User_Info())
            ->createRow($userConsultData)
            ->save();
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
     * Creates a urlkeyAction for password reset and notifies user by email
     * @param  string   $email  The email for which the password shoulb be changed
     * @return boolean          True on success, false if no user matched the email
     */
    public function recoverPassword($email)
    {
        $user = $this->fetchRow($this->select()->where('email=?', $email));
        if ($user) {
            $action = (new Service_UrlkeyAction_ResetPassword())->create(
                [Service_UrlkeyAction_ResetPassword::PARAM_USER_ID => $user->uid]
            );

            $mailer = new Dbjr_Mail();
            $mailer
                ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_PASSWORD_RESET)
                ->setPlaceholders(
                    array(
                        'to_name' => $user->name ? $user->name : $user->email,
                        'to_email' => $user->email,
                        'password_reset_url' => Zend_Registry::get('baseUrl') . '/urlkey-action/execute/urlkey/' . $action->getUrlkey(),
                    )
                )
                ->addTo($user->email);
            $emailService = new Service_Email();
            $emailService
                ->queueForSend($mailer)
                ->sendQueued();

            return true;
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
     * Sends an email asking user to confirm his/her unconfirmed inputs from the given consultation if there are any
     * @param  integer|object $identity  Either the user object or a user id
     * @param  integer        $kid       The consultation identifier.
     * @param  boolean        $isNew     Indicates if the recipient has been just created
     * @throws Dbjr_Exception            If the user can not be found in the system
     */
    public function sendInputsConfirmationMail($identity, $kid, $confirmKey, $isNew)
    {
        $intVal = new Zend_Validate_Int();
        if ($intVal->isValid($identity)) {
            $userRow = $this->find($identity)->current();
        } elseif (is_object($identity)) {
            $userRow = $identity;
        }

        if (!isset($userRow)) {
            throw new Dbjr_Exception('Trying to send input confirmation email to a non existant user.');
        }

        $inputModel = new Model_Inputs();
        $unconfirmedInputs = $inputModel->fetchAll(
            $inputModel
                ->select()
                ->setIntegrityCheck(false)
                ->from(['i' => $inputModel->info(Model_Inputs::NAME)])
                ->join(
                    array('q' => (new Model_Questions())->info(Model_Questions::NAME)),
                    'q.qi = i.qi',
                    array()
                )
                ->where('user_conf=?', 'u')
                ->where('confirmation_key=?', $confirmKey)
                ->where('q.kid=?', $kid)
        );

        if (count($unconfirmedInputs) > 0) {
            $inputIds = array();
            $inputsText = '';
            $inputsHtml = '';
            foreach ($unconfirmedInputs as $input) {
                $inputIds[] = $input->tid;
                $inputsText .= $input->thes . "\n\n";
                $inputsHtml .= '<p>' . $input->thes . '</p>';
            }

            $date = new Zend_Date();
            $baseUrl = Zend_Registry::get('baseUrl');
            $consultation = (new Model_Consultations())->find($kid)->current();
            if ($isNew) {
                $template = Model_Mail_Template::SYSTEM_TEMPLATE_INPUT_CONFIRMATION_NEW_USER;
            } else {
                $template = Model_Mail_Template::SYSTEM_TEMPLATE_INPUT_CONFIRMATION;
            }

            $mailer = new Dbjr_Mail();
            $mailer
                ->setTemplate($template)
                ->setPlaceholders(
                    array(
                        'to_name' => $userRow->name ? $userRow->name : $userRow->email,
                        'to_email' => $userRow->email,
                        'confirmation_url' =>  $baseUrl . '/input/mailconfirm/kid/' . $kid . '/ckey/' . $confirmKey,
                        'rejection_url' => $baseUrl . '/input/mailreject/kid/' . $kid . '/ckey/' . $confirmKey,
                        'consultation_title_long' => $consultation ? $consultation->titl : '',
                        'consultation_title_short' => $consultation ? $consultation->titl_short : '',
                        'input_phase_end' => $consultation ? $date->set($consultation->inp_to)->get(Zend_Date::DATE_MEDIUM) : '',
                        'input_phase_start' => $consultation ? $date->set($consultation->inp_fr)->get(Zend_Date::DATE_MEDIUM) : '',
                        'inputs_html' => $inputsHtml,
                        'inputs_text' => $inputsText,
                    )
                )
                ->addTo($userRow->email);
            (new Service_Email)->queueForSend($mailer);
        }
    }

    /**
     * Confirms user
     * @param  string         $confirmKey  The user data identifier. Identifies the session data that is to be confirmed.
     * @return integer|false               The user id of the owner fo the inputs. False if no user could be found.
     */
    public function confirmByCkey($confirmKey)
    {
        $userConsultDataModel = new Model_User_Info();
        $userConsultData = $userConsultDataModel->fetchRow(
            $userConsultDataModel
                ->select()
                ->where('confirmation_key=?', $confirmKey)
        );
        $user = $this->find($userConsultData->uid)->current();

        if ($user) {
            $user->block = 'c';
            $user->name = $userConsultData->name;
            $user->name_group = $userConsultData->name_group;
            $user->name_pers = $userConsultData->name_pers;
            $user->is_contrib_under_cc = $userConsultData->is_contrib_under_cc;
            $user->age_group = $userConsultData->age_group;
            $user->regio_pax = $userConsultData->regio_pax;
            $user->cnslt_results = $userConsultData->cnslt_results;
            $user->newsl_subscr = $userConsultData->newsl_subscr;
            $user->source = $userConsultData->source;
            $user->src_misc = $userConsultData->src_misc;
            $user->group_size = $userConsultData->group_size;
            $user->save();

            $userConsultData->confirmation_key = null;
            $userConsultData->time_user_confirmed = new Zend_Db_Expr('NOW()');
            $userConsultData->save();

            return $userConsultData->uid;
        }

        return false;
    }

    /**
     * Checks if given email address already exists in database
     * @param  string   $email  The email to be checked
     * @return boolean          Indicates if the email address exists in the system
     */
    public function emailExists($email)
    {
        $row = $this->fetchRow(
            $this
                ->select()
                ->from($this->info(self::NAME), array(new Zend_Db_Expr('COUNT(*) AS count')))
                ->where('email=?', $email)
        );

        return $row->count > 0 ? true : false;
    }

    /**
     * Updates value of last activity with current timestamp
     * @param  integer  $uid  The user identifier
     */
    public function ping($uid)
    {
        $this->update(['last_act' => new Zend_Db_Expr('NOW()')], ['uid=?' => $uid]);
    }

    /**
     * Returns all participants of given consultation.
     * @param  integer              $kid              Consultation Id
     * @param  string               $participantType  The type of participant (see: Model_User_Info::PARTICIPANT_TYPE_*)
     * @throws Dbjr_Exception                         If the requested participantType is invalid
     * @return Zend_Db_Table_Rowset                   The participants matching criteria
     */
    public function getParticipantsByConsultation($kid, $participantType = null)
    {
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from(['u' => $this->info(self::NAME)])
            ->where('u.block=?', 'c');

        if ($participantType === Model_User_Info::PARTICIPANT_TYPE_VOTER) {
            $select
                ->distinct()
                ->join(
                    ['vg' => (new Model_Votes_Groups())->info(Model_Votes_Groups::NAME)],
                    'vg.uid = u.uid',
                    []
                )
                ->where('kid=?', $kid);
        } else {
            $select
                ->join(
                    ['ui' => (new Model_User_Info())->info(Model_User_Info::NAME)],
                    'u.uid = ui.uid'
                )
                ->where('ui.kid=?', $kid)
                ->where('ui.confirmation_key IS NULL');
            if ($participantType === Model_User_Info::PARTICIPANT_TYPE_NEWSLETTER_SUBSCRIBER) {
                $select->where('u.newsl_subscr=?', 'y');
            } elseif ($participantType === Model_User_Info::PARTICIPANT_TYPE_FOLLOWUP_SUBSCRIBER) {
                $select->where('ui.cnslt_results=?', 'y');
            }
        }

        return $this->fetchAll($select);
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

    /**
     * Modifies the Select object to also select info about the number of inputs for each user
     * @param  Zend_Db_Select  $select     The select object to modify
     * @param  string          $tableAlias The table alias to be used for the subquery
     * @return Zend_Db_Select              The modified select
     */
    public function selectInputCount(Zend_Db_Select $select, $tableAlias)
    {
        $inputModel = new Model_Inputs();
        $select->join(
            [
                $tableAlias => new Zend_Db_Expr(
                    '(SELECT qi, uid, COUNT(*) AS inputCount FROM '
                    . $this->getAdapter()->quoteIdentifier($inputModel->info(Model_Inputs::NAME))
                    . ' GROUP BY uid)'
                )
            ],
            $this->info(self::NAME) . '.uid = ' . $this->getAdapter()->quoteIdentifier($tableAlias) . '.uid'
        );

        return $select;
    }
}
