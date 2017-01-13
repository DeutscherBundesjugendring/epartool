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
            $data['uid'] = $this->add(
                [
                    'block' => 'u',
                    'email' => $data['email'],
                    'newsl_subscr' => 'n',
                ]
            );
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
            'kid' => $data['kid'],
            'date_added' => new Zend_Db_Expr('NOW()'),
            'confirmation_key' => $this->_auth->hasIdentity() ? null : $confirmKey,
            'time_user_confirmed' => new Zend_Db_Expr('NOW()'),
            'age_group' => $data['age_group'] === "" ? null : $data['age_group'],
        ];

        foreach (['newsl_subscr', 'regio_pax', 'cmnt_ext', 'cnslt_results', 'name'] as $property) {
            if (isset($data[$property])) {
                $userConsultData[$property] = $data[$property];
            }
        }

        // if group then also save group specifications
        if (isset($data['group_specs'])) {

            $userConsultData['source'] = is_array($data['group_specs']['source'])
                ? implode(',', $data['group_specs']['source'])
                : null;

            foreach (['src_misc', 'group_size', 'name_group', 'name_pers'] as $property) {
                if (isset($data['group_specs'][$property])) {
                    $userConsultData[$property] = $data['group_specs'][$property];
                }
            }
        } else {
            $userConsultData = array_merge($userConsultData, [
                'group_size' => (new Model_GroupSize())->getInitGroupSize($data['kid'])['id'],
            ]);
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
        $salt = '$2y$' . $passConf->costParam . '$' . bin2hex(openssl_random_pseudo_bytes(22));

        return crypt($password, $salt);
    }

    /**
     * Sends an email asking user to confirm his/her unconfirmed inputs from the given consultation if there are any
     * @param  integer|object $identity  Either the user object or a user id
     * @param  integer        $kid       The consultation identifier.
     * @param  string         $confirmKey
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
                    array('q')
                )
                ->where('user_conf=?', 'u')
                ->where('confirmation_key=?', $confirmKey)
                ->where('q.kid=?', $kid)
        );

        if (count($unconfirmedInputs) > 0) {
            $inputIds = array();
            $inputsText = '';
            $inputsHtml = '';
            $unconfInputsSorted = [];
            $questions = [];

            foreach ($unconfirmedInputs as $input) {
                $unconfInputsSorted[$input->qi][] = $input;
                $questions[$input->qi] = $input->q;
            }
            foreach ($unconfInputsSorted as $questionId => $qInputs) {
                $inputsText .= $questions[$questionId] . "\n" . '---------------------------------' . "\n\n";
                $inputsHtml .= '<b>' . $questions[$questionId] . '</b>';
                foreach ($qInputs as $input) {
                    $inputIds[] = $input->tid;
                    $inputsText .= $input->thes . "\n\n";
                    $inputsHtml .= '<p>' . $input->thes . '</p>';
                }
            }

            $baseUrl = Zend_Registry::get('baseUrl');
            $consultation = (new Model_Consultations())->find($kid)->current();
            if ($isNew) {
                $template = Model_Mail_Template::SYSTEM_TEMPLATE_INPUT_CONFIRMATION_NEW_USER;
            } else {
                $template = Model_Mail_Template::SYSTEM_TEMPLATE_INPUT_CONFIRMATION;
            }

            $view = Zend_Controller_Front::getInstance()
                ->getParam('bootstrap')
                ->getResource('view');

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
                        'input_phase_end' => $consultation ? $view->formatDate($consultation->inp_to, Zend_Date::DATE_MEDIUM) : '',
                        'input_phase_start' => $consultation ? $view->formatDate($consultation->inp_fr, Zend_Date::DATE_MEDIUM) : '',
                        'inputs_html' => $inputsHtml,
                        'inputs_text' => $inputsText,
                    )
                )
                ->addTo($userRow->email);
            (new Service_Email)
                ->queueForSend($mailer)
                ->sendQueued();
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

        if ($userConsultData->age_group !== null) {
            $ageGroup = (new Model_ContributorAge())->find($userConsultData->age_group)->current();
        } else {
            $ageGroup = ['from' => null, 'to' => null];
        }

        if ($user) {
            $user->block = 'c';
            $user->name = $userConsultData->name;
            $user->name_group = $userConsultData->name_group;
            $user->name_pers = $userConsultData->name_pers;
            $user->age_group_from = $ageGroup['from'];
            $user->age_group_to = $ageGroup['to'];
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
            ->where('u.block=?', 'c')
            ->group('u.uid')
            ->order('u.email');

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
                    'u.uid = ui.uid',
                    ['invitation_sent_date']
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
     * @param int $kid
     * @return \Zend_Db_Table_Rowset_Abstract
     * @throws \Zend_Db_Table_Exception
     */
    public function getParticipantsByConsultationWithVotingRights($kid)
    {
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from(['u' => $this->info(self::NAME)])
            ->where('u.block=?', 'c')
            ->group('u.uid')
            ->order('u.email');

        $select
            ->join(
                ['ui' => (new Model_User_Info())->info(Model_User_Info::NAME)],
                'u.uid = ui.uid',
                ['invitation_sent_date']
            )
            ->join(
                ['vtr' => (new Model_Votes_Rights())->info(Model_Votes_Rights::NAME)],
                'vtr.kid = ui.kid AND vtr.uid = ui.uid'
            )
            ->where('ui.kid=?', $kid)
            ->where('ui.confirmation_key IS NULL');

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
        $select->where("block ='c'")->order(['name', 'email']);

        return $this->fetchAll($select)->toArray();
    }

    /**
     * @param int $consultationId
     * @return \Zend_Db_Table_Rowset_Abstract
     * @throws \Zend_Db_Table_Exception
     */
    public function getWithoutVotingRights($consultationId)
    {
        $userInfoTable = (new Model_User_Info())->info(Model_User_Info::NAME);
        $votingRightsTable = (new Model_Votes_Rights())->info(Model_Votes_Rights::NAME);

        $select = $this->select()->from(['u' => $this->info(self::NAME)])
            ->joinLeft(
                ($userInfoTable),
                'u.uid = ' . $userInfoTable . '.uid AND ' . $userInfoTable . '.kid = ' . $consultationId,
                []
            )
            ->joinLeft(
                ($votingRightsTable),
                'u.uid = ' . $votingRightsTable . '.uid AND ' . $votingRightsTable . '.kid = ' . $consultationId,
                []
            )
            ->where($userInfoTable . '.uid IS NULL')
            ->where($votingRightsTable . '.uid IS NULL')
            ->order(['u.name', 'u.email']);

        return $this->fetchAll($select);
    }

    /**
     * @param \Zend_Db_Table_Row $user
     * @param array $data
     * @return mixed
     * @throws \Zend_Auth_Exception
     */
    public function updateProfile(Zend_Db_Table_Row $user, array $data)
    {
        unset($data['password_confirm']);
        unset($data['email']);

        $data['name'] = empty($data['name']) ? null : $data['name'];
        $data['nick'] = empty($data['nick']) ? null : $data['nick'];

        if (!empty($data['password'])) {
            if (crypt($data['current_password'], $user['password']) == $user['password']) {
                $data['password'] = $this->hashPassword($data['password']);
            } else {
                throw new Zend_Auth_Exception();
            }
        } else {
            unset($data['password']);
        }
        $user->setFromArray($data);
        $result = $user->save();
        $this->updateAuthIdentity($data);
        return $result;
    }

    /**
     * @param array $newUserInfo
     */
    private function updateAuthIdentity($newUserInfo)
    {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();

        foreach (['name', 'nick'] as $key) {
            $identity->{$key} = empty($newUserInfo[$key]) ? null : $newUserInfo[$key];
        }
    }
}
