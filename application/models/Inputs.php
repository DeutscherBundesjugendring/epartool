<?php

class Model_Inputs extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'inpt';
    protected $_primary = 'tid';

    protected $_dependentTables = array('Model_InputsTags');

    protected $_referenceMap = array(
        'Questions' => array(
            'columns' => 'qi', 'refTableClass' => 'Model_Questions', 'refColumns' => 'qi'
        )
    );

    protected $_flashMessenger = null;

    protected $_auth = null;

    public function init()
    {
        $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
        $this->_auth = Zend_Auth::getInstance();
    }

    /**
     * getById
     * @desc returns entry by id
     * @name getById
     * @param  integer $id
     * @param  integer $tag [optional] Filter nach Tag
     * @return array
     */
    public function getById($id, $tag = 0)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return array();
        }

        $row = $this->find($id)->current();
        $subrow1 = $row->findManyToManyRowset('Model_Tags', 'Model_InputsTags')->toArray();

        $result = $row->toArray();
        $result['tags'] = $subrow1;
        if ($tag > 0) {
            $deliverRecord = false;
            foreach ($result['tags'] as $value) {
                if ($tag == $value['tg_nr']) {
                    $deliverRecord = true;
                }
            }
            if (!$deliverRecord) {
                $result = null;
            }
        }

        return $result;
    }

    /**
     * add
     * @desc add new entry to db-table
     * @name add
     * @param  array   $data
     * @return integer primary key of inserted entry
     */
    public function add($data)
    {

        if (isset($data['video_id']) && empty($data['video_id'])) {
            $data['video_service'] = null;
            $data['video_id'] = null;
        }
        
        $row = $this->createRow($data);

        $contributionId = (int) $row->save();
        
        $modelInputsTags = new Model_InputsTags();
        if (!empty($data['tags'])) {
            $modelInputsTags->insertByInputsId($contributionId, $data['tags']);
        }

        return $contributionId;
    }

    /**
     * updateById
     * @param  integer $id
     * @param  array   $data
     * @return integer
     */
    public function updateById($id, $data)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return 0;
        }
        // exists?
        if ($this->find($id)->count() < 1) {
            return 0;
        }

        $modelInputsTags = new Model_InputsTags();
        $modelInputsTags->deleteByInputsId($id);
        if (isset($data['tags']) && !empty($data['tags'])) {
            $inserted = $modelInputsTags->insertByInputsId($id, $data['tags']);
        }

        if (isset($data['video_id']) && empty($data['video_id'])) {
            $data['video_service'] = null;
            $data['video_id'] = null;
        }

        $row = $this->find($id)->current();
        $row->setFromArray($data);

        return $row->save();
    }

    /**
     * unlinkById
     * @param  integer $id
     * @param  integer $relId
     * @return integer
     */
    public function unlinkById($id, $relId)
    {
        if ($this->find($id)->count() < 1) {
            return 0;
        }

        $row = $this->find($id)->current();
        $related = explode(',', $row['rel_tid']);
        foreach($related as $key => $rid) {
            if($rid === $relId) {
                unset($related[$key]);
                break;
            }
        }
        $row->setFromArray(['rel_tid' => implode(',', $related)]);

        return $row->save();
    }

    /**
     * deleteById
     * @desc delete entry by id
     * @name deleteById
     * @param  integer $id
     * @return integer
     */
    public function deleteById($id)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return 0;
        }
        // exists?
        if ($this->find($id)->count() < 1) {
            return 0;
        }

        (new Model_InputsTags())->deleteByInputsId($id);
        (new Model_InputDiscussion())->delete(['input_id=?' => $id]);
        $where = $this->getDefaultAdapter()->quoteInto($this->_primary[1] . '=?', $id);
        $result = $this->delete($where);

        return $result;
    }

    /**
     * getByUser
     * @desc returns entry by user-id
     * @name getByUser
     * @param  integer $uid id of user
     * @return array
     */
    public function getByUser($uid)
    {
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
     * @param $uid
     * @param $limit
     * @return array
     * @throws \Zend_Db_Table_Exception
     */
    public function getByUserWithDependencies($uid, $limit)
    {
        // fetch
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from(['i' => $this->_name])
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )->join(
                ['cnslt' => (new Model_Consultations())->info(Model_Consultations::NAME)],
                'cnslt.kid = q.kid',
                ['kid', 'titl']
            )
            ->where('cnslt.proj = ?', $this->_projectCode)
            ->where('uid=?', $uid)
            ->order('i.when DESC')
            ->limit($limit);
        $result = $this->fetchAll($select);

        return $result->toArray();
    }

    /**
     * Returns inputs by user and consultation
     * @param integer      $uid   User ID
     * @param integer      $kid   Consultation ID
     * @param string|array $order [optional] Order specification
     */
    public function getByUserAndConsultation($uid, $kid, $order = null)
    {
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from(['i' => $this->_name])
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )
            ->where('i.uid=?', $uid)
            ->where('q.kid=?', $kid);
        if ($order) {
            $select->order($order);
        }

        return $this->fetchAll($select);
    }

    /**
     * getByQuestion
     * @desc returns entry by question-id
     * @name getByQuestion
     * @param  integer      $qid   id of question (qi in mysql-table)
     * @param  string|array $order [optional] MySQL Order Expression, e.g. 'votes DESC'
     * @param  integer      $limit [optional] Number of records to return
     * @param  integer      $tag   [optional] id of tag (tg_nr)
     * @param  bool         $withoutAdmin [optional]
     * @return array
     */
    public function getByQuestion($qid, $order = 'i.tid ASC', $limit = null, $tag = null, $withoutAdmin = false)
    {
        // is int?
        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($qid)) {
            return array();
        }

        // get select obj
        $select = $this->getSelectByQuestion($qid, $order, $limit, $tag, $withoutAdmin);

        $stmt = $this->getDefaultAdapter()->query($select);
        $result = $stmt->fetchAll();

        return $result;
    }

    /**
     * Returns number of inputs for a consultation
     * @param  integer $kid               The consultattion identifier
     * @param  boolean $excludeInvisible  Default: true
     * @param  boolean $withoutAdmin
     * @return integer                    The number of inputs
     */
    public function getCountByConsultation($kid, $excludeInvisible = true, $withoutAdmin = true)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                ['i' => $this->info(self::NAME)],
                [new Zend_Db_Expr('COUNT(*) as count')]
            )
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )
            ->where('q.kid=?', $kid);

        if ($excludeInvisible) {
            $select
                ->where('block<>?', 'y')
                ->where('user_conf=?', 'c');
        }
        
        if ($withoutAdmin) {
            $select->where('(uid IS NOT NULL OR confirmation_key IS NOT NULL)');
        }

        return $this->fetchAll($select)->current()->count;
    }

    /**
     * Returns number of inputs for a user
     * @param  integer $uid
     * @return integer
     */
    public function getCountByUser($uid)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['i' => $this->_name], array(new Zend_Db_Expr('COUNT(*) as count')))
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )->join(
                ['cnslt' => (new Model_Consultations())->info(Model_Consultations::NAME)],
                'cnslt.kid = q.kid',
                ['kid', 'titl']
            )
            ->where('cnslt.proj = ?', $this->_projectCode)
            ->where('uid = ?', $uid);

        $row = $this->fetchAll($select)->current();

        return $row->count;
    }

    /**
     * Returns array with count of input of a user filtered by consultation
     *
     * @param  integer $uid
     * @return integer
     */
    public function getCountByUserGroupedConsultation($uid)
    {
        $return = array();
        $db = $this->getDefaultAdapter();
        $select = $db->select()
        ->from('inpt as i', array(new Zend_Db_Expr('COUNT(i.tid) as count, i.kid')))
        ->joinLeft('cnslt as c', 'i.kid=c.kid',array('titl'))
        ->group('i.kid')
        ->where('i.uid = ?', $uid);
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        foreach ($row AS $curRow) {
            $return[] = $curRow;
        }

        return $return;
    }

    /**
     * Returns number of inputs for a consultation, filtered by given conditions
     * @param  integer $kid
     * @param  array   $filter [optional] array(array('field' => $field, 'operator' => $operator, 'value' => $value)[, ...])
     * @return integer
     */
    public function getCountByConsultationFiltered($kid, $filter = array())
    {
        $select = $this->select()
            ->from(['i' => $this->info(self::NAME)], [new Zend_Db_Expr('COUNT(*) as count')])
            ->setIntegrityCheck(false)
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )
            ->where('kid = ?', $kid);


        foreach ($filter as $condition) {
            if (is_array($condition)) {
                $select->where(
                    $this->getDefaultAdapter()->quoteIdentifier($condition['field']) . ' '
                    . $condition['operator'] . ' ?',
                    $condition['value']
                );
            }
        }
        $row = $this->fetchAll($select)->current();

        return $row->count;
    }

    /**
     * Returns number of inputs for a question
     * @param  integer $qid
     * @param  integer $tag              [optional]
     * @param  boolean $excludeInvisible [optional], Default: true
     * @param  boolean $withoutAdmin
     * @return integer
     */
    public function getCountByQuestion($qid, $tag = null, $excludeInvisible = true, $withoutAdmin = true)
    {
        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($qid)) {
            throw new Zend_Validate_Exception('Given parameter qid must be integer!');
        }

        $db = $this->getDefaultAdapter();
        $select = $db->select();
        $select
            ->from(
                array('i' => $this->_name),
                array(new Zend_Db_Expr('COUNT(*) as count'))
            )
            ->where('i.qi = ?', $qid);
        if ($excludeInvisible) {
            $select
                ->where('i.block<>?', 'y')
                ->where('i.user_conf=?', 'c');
        }

        if ($withoutAdmin) {
            $select->where('(i.uid IS NOT NULL OR i.confirmation_key IS NOT NULL)');
        }

        if ($intVal->isValid($tag)) {
            $select->joinLeft(array('it' => 'inpt_tgs'), 'i.tid = it.tid', array());
            $select->where('it.tg_nr = ?', $tag);
        }

        $stmt = $db->query($select);
        $row = $stmt->fetch();

        return $row['count'];
    }

    /**
     * Returns number of inputs for a question, filtered by given conditions
     * @param  integer $qid
     * @param  array   $filter [optional] array(array('field' => $field, 'operator' => $operator, 'value' => $value)[, ...])
     * @return integer
     */
    public function getCountByQuestionFiltered($qid, $filter = array())
    {
        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($qid)) {
            throw new Zend_Validate_Exception('Given parameter qid must be integer!');
        }

        $select = $this->select()
            ->from($this, array(new Zend_Db_Expr('COUNT(*) as count')))
            ->where('qi = ?', $qid)
            ->where('uid <> ?', 1);

        if (!empty($filter)) {
            foreach ($filter as $condition) {
                if (is_array($condition)) {
                    $select->where(
                        $this->getDefaultAdapter()->quoteIdentifier($condition['field']) . ' '
                        . $condition['operator'] . ' ?',
                        $condition['value']
                    );
                }
            }
        }

        $row = $this->fetchAll($select)->current();

        return $row->count;
    }

    /**
     * Returns Zend_Db_Select for use in e.g. Paginator
     * @param  integer        $qid
     * @param  string|array   $order
     * @param  integer        $limit
     * @param  integer        $tag   [optional] id of tag (tg_nr)
     * @param  bool           $withoutAdmin [optional]
     * @return Zend_Db_Select
     */
    public function getSelectByQuestion($qid, $order = 'i.tid DESC', $limit = null, $tag = null, $withoutAdmin = false)
    {
        $intVal = new Zend_Validate_Int();
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from(array('i' => $this->_name))
            ->joinLeft(
                ['id' => 'input_discussion'],
                'id.input_id = i.tid AND id.is_user_confirmed=1',
                ['discussionPostCount' => 'COUNT(id.input_id)']
            );

        if ($intVal->isValid($tag)) {
            $select
                ->joinLeft(array('it' => 'inpt_tgs'), 'i.tid = it.tid', array())
                ->where('it.tg_nr = ?', $tag);
        }

        if ($withoutAdmin) {
            $select->where('(i.uid IS NOT NULL OR i.confirmation_key IS NOT NULL)');
        }

        $select
            ->where('i.qi=?', $qid)
            ->where('i.block!=?', 'y')
            ->where('i.user_conf=?', 'c')
            ->group('i.tid');

        if ($order) {
            $select->order($order);
        }

        if ($intVal->isValid($limit)) {
            $select->limit($limit);
        }

        return $select;
    }

    /**
     * Confirms inputs and confirms user registration if applicable
     * @param  string    $confirmKey  The confirmation key identyfying the inputs to be confirmed
     * @param  integer   $uid         The user identifier in cases when user is already confirmed (i.e. social login)
     * @return integer                Number of inputs confirmed
     */
    public function confirmByCkey($confirmKey, $uid = null)
    {
        $this->isConfirmOrRejectAllowed($confirmKey);

        $userModel = new Model_Users();
        if (!$uid) {
            $uid = $userModel->confirmByCkey($confirmKey);
        }
        $userModel->ping($uid);

        return $this->update(
            [
                'user_conf' => 'c',
                'uid' => $uid,
                'confirmation_key' => null
            ],
            ['confirmation_key=?' => $confirmKey, 'user_conf=?' => 'u']
        );
    }

    /**
     * Rejects inputs and confirms user registration if applicable
     * @param  string    $confirmKey  The confirmation key identyfying the inputs to be confirmed
     * @return integer                Number of inputs rejected
     */
    public function rejectByCkey($confirmKey)
    {
        $this->isConfirmOrRejectAllowed($confirmKey);
        $userConsultDataModel = new Model_User_Info();
        $uid = $userConsultDataModel->fetchRow(
            $userConsultDataModel
                ->select()
                ->from($userConsultDataModel->info(Model_User_Info::NAME), ['uid'])
                ->where('confirmation_key=?', $confirmKey)
        )->uid;

        $userModel = new Model_Users();
        $userModel->ping($uid);

        return $this->update(
            [
                'user_conf' => 'r',
                'uid' => $uid,
                'confirmation_key' => null
            ],
            ['confirmation_key=?' => $confirmKey, 'user_conf=?' => 'u']
        );
    }

    /**
     * Throws excpetion if rejection/confirmation are not allowed
     * @throws Dbjr_UrlkeyAction_Exception  If the consultation has moved past the input phase
     */
    private function isConfirmOrRejectAllowed($confirmKey)
    {
        $inputPhaseTo = $this->fetchRow(
            (new Model_Consultations())
                ->select()
                ->setIntegrityCheck(false)
                ->from(['i' => $this->info(Model_Inputs::NAME)], [])
                ->join(['q' => (new Model_Questions())->info(Model_Questions::NAME)], 'i.qi=q.qi', [])
                ->join(['c' => (new Model_Consultations())->info(Model_Consultations::NAME)], 'q.kid=c.kid', ['inp_to'])
                ->where('i.confirmation_key=?', $confirmKey)
        )->inp_to;

        if (Zend_Date::now()->isLater(new Zend_Date($inputPhaseTo, Zend_Date::ISO_8601))) {
            throw new Dbjr_UrlkeyAction_Exception('Cant confirm or reject once the input phase is over');
        }
    }

    /**
     * Deletes several entries at once
     * @param  array   $ids Array of integer values (Input IDs)
     * @return integer Number of deleted entries
     */
    public function deleteBulk($ids)
    {
        $nrDeleted = 0;
        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $id) {
                $nr = $this->deleteById($id);
                $nrDeleted+= $nr;
            }
        }

        return $nrDeleted;
    }

    /**
     * Saves changes for several entries at once
     * @param  array   $ids   Array of input identifiers
     * @param  array   $data  Key value pairs of data to be updated for all specified inputs
     * @return integer        The number of inputs that got edited
     */
    public function editBulk($ids, $data)
    {
        if (isset($data['video_id']) && empty($data['video_id'])) {
            $data['video_service'] = null;
            $data['video_id'] = null;
        }
        
        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $id) {
                $this->updateById($id, $data);
            }
        }

        return count($ids);
    }

    /**
     * Returns inputs by user and consultation grouped by question
     * for the user inputs overview
     * @param  integer $uid
     * @param  integer $kid
     * @return array
     */
    public function getUserEntriesOverview($uid, $kid)
    {
        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($uid)) {
            throw new Zend_Validate_Exception('Given kid must be integer!');
        }
        if (!$intVal->isValid($kid)) {
            throw new Zend_Validate_Exception('Given uid must be integer!');
        }
        $entries = array();
        $entriesRaw = $this->getByUserAndConsultation($uid, $kid);

        $questionModel = new Model_Questions();
        $questions = $questionModel->getByConsultation($kid);
        foreach ($questions as $question) {
            $entries[$question['qi']] = $question->toArray();
        }
        foreach ($entriesRaw as $rawEntry) {
            $entries[$rawEntry['qi']]['inputs'][] = $rawEntry->toArray();
        }

        return $entries;
    }

    /**
     * Returns formatted CSV string
     * @param  integer                 $kid
     * @param  integer                 $qid
     * @param  string                  $mod
     * @param  integer                 $tag [optional]
     * @throws Zend_Validate_Exception
     * @return string
     */
    public function getCSV($kid, $qid, $mod, $tag = null)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($kid)) {
            throw new Zend_Validate_Exception('Given parameter kid must be integer!');
        }
        if (!$validator->isValid($qid)) {
            throw new Zend_Validate_Exception('Given parameter qid must be integer!');
        }
        if (!empty($tag)) {
            if (!$validator->isValid($tag)) {
                throw new Zend_Validate_Exception('Given parameter tag must be integer!');
            }
        }
        $csv = '';
        $db = $this->getAdapter();
        $select = $db->select()
            ->from(['i' => 'inpt'])
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )
            ->where('kid = ?', $kid)
            ->where('q.qi = ?', $qid);

        switch ($mod) {
            case 'cnf':
                $select
                    ->where('i.user_conf = ?', 'c')
                    ->where('(i.uid IS NOT NULL OR i.confirmation_key IS NOT NULL)');
                break;
            case 'unc':
                $select
                    ->where('i.user_conf != ?', 'c')
                    ->where('(i.uid IS NOT NULL OR i.confirmation_key IS NOT NULL)');
                break;
            case 'all':
                $select->where('(i.uid IS NOT NULL OR i.confirmation_key IS NOT NULL)');
                break;
            case 'vot':
                $select->where('i.vot = ?', 'y');
                break;
            case 'edt':
                $select
                    ->where('i.vot = ?', 'y')
                    ->where('i.uid IS NULL AND i.confirmation_key IS NULL');
                break;
        }
        $select->joinLeft(
            array('it' => 'inpt_tgs'),
            'i.tid = it.tid',
            array()
        );
        $select->joinLeft(
            array('t' => 'tgs'),
            'it.tg_nr = t.tg_nr',
            array('tags' => "GROUP_CONCAT(t.tg_de ORDER BY t.tg_de SEPARATOR ',')")
        );
        $select->group('i.tid');

        if (!empty($tag)) {
            $select->where('it.tg_nr = ?', $tag);
        }

        $consultationModel = new Model_Consultations();
        $consultation = $consultationModel->find($kid)->current()->toArray();
        if (!empty($consultation)) {
            $csv.= '"Beteiligungsrunde";"' . $consultation['titl'] . '"' . "\r\n";
        } else {
            return 'Beteiligungsrunde nicht gefunden!';
        }

        $questionModel = new Model_Questions();
        $question = $questionModel->find($qid)->current()->toArray();
        if (!empty($question)) {
            $csv.= '"' .(isset($question['nr']) ? $question['nr'] : '') . '";"' . $question['q'] . '"' . "\r\n\r\n";
        } else {
            return 'Frage nicht gefunden!';
        }

        $csv.='"THESEN-ID";"BEITRAG";"ERKLÄRUNG";"SCHLAGWÖRTER";"NOTIZEN"' . "\r\n";

        $stmt = $db->query($select);
        $rowSet = $stmt->fetchAll();
        foreach ($rowSet as $row) {
            $csv.='"' . $row['tid'] . '";"'
                . html_entity_decode($row['thes'], ENT_COMPAT, 'UTF-8') . '";"'
                . html_entity_decode($row['expl'], ENT_COMPAT, 'UTF-8') . '";"'
                . $row['tags'] . '"' . "\r\n";
        }

        return $csv;
    }

    /**
     * Adds one point to specified input and returns the new number of supports
     * @param  integer $tid  The identifier of the input
     * @return integer       The updated number of inputs or 0 if no inut could be found
     */
    public function addSupport($tid)
    {
        $row = $this->find($tid)->current();
        if ($row) {
            $consultationModel = new Model_Consultations();
            $consultation = $consultationModel->fetchRow(
                $consultationModel
                    ->select()
                    ->from(['c' => 'cnslt'], ['spprt_fr', 'spprt_to'])
                    ->join(['q' => 'quests'], 'q.kid = c.kid', [])
                    ->where('q.qi=?', $row->qi)
            );

            if (Zend_Date::now()->isLater(new Zend_Date($consultation->spprt_fr, Zend_Date::ISO_8601))
                && Zend_Date::now()->isEarlier(new Zend_Date($consultation->spprt_to, Zend_Date::ISO_8601))
                || $consultation->spprt_fr !== '0000-00-00 00:00:00'
                || $consultation->spprt_to !== '0000-00-00 00:00:00'
                || $consultation->spprt_show === 'y'
            ) {
                $row->spprts++;
                $row->save();
            }
        }

        return !empty($row->spprts) ? $row->spprts : 0;
    }

    /**
     * Search in questions by consultations
     * @param  string  $needle
     * @param  integer $consultationId
     * @param  integer $limit
     * @return array
     */
    public function search($needle, $consultationId, $limit = 30)
    {
        if ($needle !== '' && !empty($consultationId) && is_int($limit)) {
            $select = $this
                ->select()
                ->from(
                    ['i'=>'inpt'],
                    ['expl'=>'SUBSTRING(expl,1,100)', 'qi', 'tid', 'thes']
                )
                ->where(
                    $this->getAdapter()->quoteInto('i.thes LIKE ?', "%$needle%")
                    . $this->getAdapter()->quoteInto(' OR i.expl LIKE ?', "%$needle%")
                )
                ->join(
                    ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                    'q.qi = i.qi',
                    []
                )
                ->where('i.block!=?', 'y')
                ->where('i.user_conf=?', 'c')
                ->where('q.kid=?', $consultationId)
                ->limit($limit);

            $result = $this->fetchAll($select)->toArray();
        }

        return isset($result) ? $result : [];
    }

    /**
     * Returns number of users who added at least one input to given consultation
     * @param  integer                 $kid
     * @throws Zend_Validate_Exception
     * @return integer
     */
    public function getCountParticipantsByConsultation($kid)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($kid)) {
            throw new Zend_Validate_Exception('Given parameter kid must be integer!');
        }

        return $this
            ->fetchAll(
                $this->select()
                    ->distinct()
                    ->setIntegrityCheck(false)
                    ->from(['i' => $this->info(self::NAME)], ['uid'])
                    ->join(
                        ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                        'q.qi = i.qi',
                        []
                    )
                    ->where('kid = ?', $kid)
                    ->where('uid IS NOT NULL')
            )
            ->count();
    }

    /**
     * Liefert eine CSV-Liste aller abzustimmenen Beiträge einer Konsultation
     * @param  inter $kid
     * @return array
     */
    public function getVotingchain($kid)
    {
        if (empty($kid)) {
            return array();
        }

        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from(['i' => $this->_name])
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )
            ->where('i.vot=?', 'y')
            ->where('q.kid=?', $kid);

        $rows = $this->fetchAll($select)->toArray();
        $tlist = array();
        $qlist = array();
        foreach ($rows as $inpt) {
            $tlist[]=$inpt['tid'];
            $qlist[]=$inpt['qi'];
        }
        $list = array(
            'tid'=>$tlist,
            'qi' =>$qlist
        );

        return $list;
    }

    public function getThesisbyQuestion($kid, $qid)
    {
        if (empty($kid) || empty($qid)) {
            return array();
        }

        $result = array();

        $select = $this->select();
        $select->where('kid=?', $kid);
        $select->where('qi=?', $qid);
        $select->where('vot=?', 'y');

        $rowSet = $this->fetchAll($select)->toArray();
        foreach ($rowSet as $row) {
            $result[$row['tid']] = $row;
        }

        return $result;
    }

    public function getThesisbyTag($kid, $tagId)
    {
        if (empty($kid) || empty($tagId)) {
            return array();
        }

        $result = array();
        $db = $this->getAdapter();
        $select = $db
            ->select()
            ->from(array('it' => 'inpt_tgs'))
            ->joinLeft(array('i' => 'inpt'), 'i.tid = it.tid')
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )
            ->where('i.kid=?', $kid)
            ->where('i.vot=?', 'y')
            ->where('it.tg_nr = ?', (int) $tagId);

        $stmt = $db->query($select);
        $rowSet = $stmt->fetchAll();

        if ($rowSet) {
            $result = $rowSet;
        }

        return $result;

    }

    /**
     * Migrate tags in csv-form from table inputs
     * to db-relation table inpt_tgs
     * DONT USE IN LIVE-SYSTEM
     */
    public function migrateTags()
    {
        $inputTagsModel = new Model_InputsTags();

        $select = $this->select();
        $rowset = $this->fetchAll($select)->toArray();

        foreach ($rowset as $input) {
            if (!empty($input['tg_nrs'])) {
                $tags = explode(',', $input['tg_nrs']);
                $inputTagsModel->insertByInputsId($input['tid'], $tags);
                // @codingStandardsIgnoreLine
                echo($input['tid'] . ':' .$input['tg_nrs'] . '<br />');
            }

        }
    }

    /**
     * Returns voting theses by question
     * @param  integer                 $qid
     * @throws Zend_Validate_Exception
     * @return Zend_Db_Table_Rowset
     */
    public function getVotingthesesByQuestion($qid)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($qid)) {
            throw new Zend_Validate_Exception('Given parameter qid must be integer!');
        }

        return $this->fetchAll(
            $this->select()
                ->where('qi = ?', $qid)
                ->where('vot = ?', 'y')
        );
    }

    /**
     * set a new owner of written input by a user and consultation
     * @param integer $uid
     * @param integer $targetUid
     * @param integer $kid
     */
    public function transferInputs($uid, $targetUid, $kid)
    {
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from(['i' => $this->info(self::NAME)], ['tid'])
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )
            ->where('uid = ?', $uid)
            ->where('kid = ?', $kid);

        $rowset = $this->fetchAll($select)->toArray();
        $tids = array_map(function($el) {return $el['tid'];}, $rowset);
        $this->update(['uid' => $targetUid], ['tid IN (?)' => $tids]);

        return true;
    }

    /**
     * getRelatedWithVotesById
     * get the referenced theses with vot = y
     * @param int $id
     * @return array
     */
    public function getRelatedWithVotesById($id)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
                return array();
        }

        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from(['i' => $this->info(self::NAME)])
            ->join(['q' => (new Model_Questions())->info(Model_Questions::NAME)], 'i.qi = q.qi', [])
            ->join(['c' => (new Model_Consultations())->info(Model_Consultations::NAME)], 'q.kid = c.kid', ['kid'])
            ->where('rel_tid LIKE ?', '%' . $id . '%')
            ->where("`vot` LIKE 'y'");
        
        $result = $this->fetchAll($select)->toArray();

        return $result;

    }

    /**
     * getFollowups
     * get the follow-ups by a given tid
     * @param int $id
     * @param string $where
     * @return array
     */
    public function getFollowups($id, $where = null)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
                   return array();
        }
        $depTable = new Model_FollowupsRef();
        $depTableSelect = $depTable->select();
        if ($where) {
                   $depTableSelect->where($where);
        }
        $result = array();
        $row = $this->find($id)->current();
        if ($row) {
            $Model_Followups = new Model_Followups();
            $rowset = $row->findDependentRowset($depTable, 'Inputs', $depTableSelect);
            $refs = $rowset->toArray();

            $fids = array();
            foreach ($refs as $ref) {
                    $fids[] = $ref['fid_ref'];
            }
            $result = $Model_Followups->getByIdArray($fids);
        }

        return $result;
    }

    /**
     * Returns inputs with tags and related inputs by given question
     * @param  array  $wheres   Associative array defining the inputs to return in Zend_Db_Select::where() format
     * @return array            An array of arrays with the input data
     */
    public function fetchAllInputs(array $wheres = [])
    {
        $select = $this->select();
        foreach ($wheres as $cond => $val) {
            $select->where($cond, $val);
        }
        $resultSet = $this->getAdapter()->query($select);

        $inputs = [];
        foreach ($resultSet as $row) {
            $inputs[$row['tid']] = $row;
            $inputs[$row['tid']]['related'] = [];

            $inputs[$row['tid']]['tags'] = [];
            $tagRows = $this->find($row["tid"])->current()
                ->findManyToManyRowset('Model_Tags', 'Model_InputsTags')
                ->toArray();
            foreach ($tagRows as $tagRow) {
                $inputs[$row['tid']]['tags'][] = $tagRow;
            }
        }

        foreach ($this->getAdapter()->query($this->select()) as $contribution) {
            if (!empty($contribution['rel_tid'])) {
                foreach (explode(',', $contribution['rel_tid']) as $relatedId) {
                    if (isset($inputs[$relatedId])) {
                        $inputs[$relatedId]['related'][] = $contribution;
                    }
                }
            }
        }

        return $inputs;
    }

    /**
     * Insert a new Input including tags
     * @param array     $data   The input data
     * @return integer          The new row identifier
     */
    public function addInputs($data)
    {
        if (isset($data['video_id']) && empty($data['video_id'])) {
            $data['video_service'] = null;
            $data['video_id'] = null;
        }
        
        $row = $this->createRow($data);
        $tid = (int) $row->save();

        if (isset($data['tags']) && !empty($data['tags'])) {
            $modelInputsTags = new Model_InputsTags();
            $modelInputsTags->deleteByInputsId($tid);
            $inserted = $modelInputsTags->insertByInputsId($tid, $data['tags']);
        }

        return $tid;
    }

    /**
     * @param int $origInputId
     * @param array $newInputIds
     * @throws Zend_Db_Table_Exception
     */
    public function appendRelIds($origInputId, array $newInputIds)
    {
        foreach ($newInputIds as $inputId) {
            $row = $this->find($inputId)->current();
            $relIds =  !empty($row['rel_tid']) ? explode(',', $row['rel_tid']) : [];
            $relIds[] = $origInputId;
            $relIds = array_unique($relIds);
            $relIds = implode(',', $relIds);
            $this->update(['rel_tid' => $relIds], $this->getAdapter()->quoteInto('tid= ?', $inputId));
        }
    }

    /**
     * Returns the number of inputs in directory for the given directory and question combination
     * @param  integer  $qid  The question identifier
     * @param  integer  $dir  The direcry identifier
     * @return integer        The number of inputs for the given question-direcory combination
     */
    public function getNumByDirectory($qid, $dir)
    {
        $select = $this
            ->select()
            ->from(array('inputs' => 'inpt'), 'COUNT(tid) as count')
            ->where('inputs.qi = ?', $qid)
            ->where('inputs.dir = ?', $dir);
        $resultSet = $this->fetchRow($select);

        return $resultSet['count'];
    }

    /**
     * getByIdArray
     * @desc returns entries by an idArray
     * @name getByIdArray
     * @param  array $tids
     * @return array
     */
    public function getByIdArray($tids)
    {
        if (!is_array($tids) || !count($tids)) {
            return array();
        }
        $select = $this->select();
        $select->where('tid IN(?)', $tids);

        return $this->fetchAll($select)->toArray();
    }

    /**
     * thesisExists
     * @desc checks thesis  by inputID and given consultionID
     * @name thesisExists()
     * @param  integer $id
     * @return bool
     */
    public function thesisExists($tid,$kid)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($tid)) {
            return false;
        }

         $select = $this->select();
         $select
         ->from(array('inputs' => 'inpt'),'COUNT(tid) as count')
         ->where('inputs.tid = ?', $tid);
          $resultSet = $this->fetchRow($select);

        if ($resultSet['count'] ==1) {
           return true;
        } else {
           return false;
        }
    }

    /**
     * Returns a string to identify all inputs that were entered together
     * The string must be unique
     * @return string The string to identify the inputs
     */
    public function getConfirmationKey()
    {
        $confirmKey = sha1(session_id() . microtime() . rand(0, 100));
        $count = $this
            ->fetchRow(
                $this
                    ->select()
                    ->from($this->info(Model_Inputs::NAME), array('count' => 'count(*)'))
                    ->where('confirmation_key=?', $confirmKey)
            )
            ->count;

        if ($count) {
            return $this->getConfirmationKey();
        }

        return $confirmKey;
    }

    /**
     * Returns the data needed to populate the input boxes in per user view
     * @param  array   $wheres   An array of [condition => value] arrays to be used in Zend_Db_Select::where()
     * @return array             An array of arrays
     */
    public function getCompleteGroupedByQuestion($wheres)
    {
        $res = $this->fetchAll($this->getInputBoxListDataSelect($wheres, 'tid'));

        $inputs = [];
        foreach ($res as $input) {
            if (!isset($inputs[$input['qi']])) {
                $inputs[$input['qi']] = [
                    'q' => $input['q'],
                    'inputs' => [],
                    'nr' => $input['nr'],
                ];
            }
            $tags = $input->findManyToManyRowset('Model_Tags', 'Model_InputsTags')->toArray();
            $input = $input->toArray();
            $input['tags'] = $tags ? $tags : [];
            $inputs[$input['qi']]['inputs'][] = $input;
        }

        return $inputs;
    }

    /**
     * Returns the data needed to populate the input boxes in per question view
     * @param  array   $wheres  An array of [condition => value] arrays to be used in Zend_Db_Select::where()
     * @param string $order
     * @return array             An array of arrays
     */
    public function getComplete($wheres, $order = 'tid')
    {
        $res = $this->fetchAll($this->getInputBoxListDataSelect($wheres, $order));

        $inputs = [];
        foreach ($res as $input) {
            $tags = $input->findManyToManyRowset('Model_Tags', 'Model_InputsTags')->toArray();
            $input = $input->toArray();
            $input['tags'] = $tags ? $tags : [];
            $inputs[] = $input;
        }

        return $inputs;
    }

    /**
     * Returns the select to be used as a base for getting the complete input data
     * @param  array            $wheres  An array of [condition => value] arrays to be used in Zend_Db_Select::where()
     * @param  string $order
     * @return Zend_Db_Select            The select object
     */
    private function getInputBoxListDataSelect($wheres, $order)
    {
        $select = $this
            ->select()
            ->from($this->info(Model_Questions::NAME), ['tid', 'thes', 'expl', 'when', 'notiz', 'block', 'vot', 'user_conf', 'input_discussion_contrib', 'spprts'])
            ->setIntegrityCheck(false)
            ->join(
                (new Model_Questions())->info(Model_Questions::NAME),
                $this->info(self::NAME) . '.qi = ' . (new Model_Questions())->info(Model_Questions::NAME) . '.qi',
                ['nr', 'q']
            )
            ->joinLeft(
                (new Model_Users())->info(Model_Users::NAME),
                (new Model_Users())->info(Model_Users::NAME) . '.uid = ' . $this->info(self::NAME) . '.uid',
                ['uid', 'name']
            )
            ->order($order);

        foreach ($wheres as $cond => $value) {
            $select->where($cond, $value);
        }

        return $select;
    }

    /**
     * Returns an aray of input ids that have discussion contributions and match the where criteria
     * @param  array $wheres The where conditions
     * @return array         An array of the input ids
     */
    public function getInputsWithDiscussionIds($wheres)
    {
        $discModel = new Model_InputDiscussion();

        $select = $discModel
            ->select()
            ->setIntegrityCheck(false)
            ->from(
                ['i' => $this->info(self::NAME)],
                ['tid']
            )
            ->join(
                ['d' => $discModel->info(Model_InputDiscussion::NAME)],
                'i.tid = d.input_id',
                []
            )
            ->group('i.tid');

        foreach ($wheres as $cond => $value) {
            $select->where($cond, $value);
        }
        $inputs = $discModel->fetchAll($select);

        $inputIds = [];
        foreach ($inputs as $input) {
            $inputIds[] = $input->tid;
        }

        return $inputIds;
    }

    /**
     * @param array $formValues
     * @throws \Exception
     * @throws \Zend_Db_Table_Exception
     */
    public function createContribution(array $formValues)
    {
        $added = $this->add($formValues);
        if ($added > 0) {
            $newContribution = $this->find($added)->current();
            $question = (new Model_Questions())->find($newContribution->qi)->current();
            $userInfoModel = new Model_User_Info();
            $userInfo = $userInfoModel->fetchRow([
                'uid = ' . $newContribution->uid,
                'kid =' . $question->kid,
            ]);
            if ($userInfo === null) {
                $userModel = new Model_Users();
                $userData = $userModel->find($newContribution->uid)->current()->toArray();
                $userData['cmnt_ext'] = "";
                $userData['kid'] = $question->kid;
                $userInfoId = $userModel->addConsultationData($userData);
                if (!$userInfoId) {
                    throw new \Exception('Adding user info failed');
                }
            }
            return;
        }
        throw new \Exception('Adding contribution failed');
    }

    /**
     * @param array $where
     * @param int $reminders
     * @return \Zend_Db_Table_Rowset_Abstract
     * @throws \Zend_Db_Table_Exception
     */
    public function getUnconfirmedContributions(array $where, $reminders = null)
    {
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from(
                ['i' => $this->info(self::NAME)]
            )
            ->join(
                ['ui' => (new Model_User_Info())->info(Model_User_Info::NAME)],
                'ui.confirmation_key = i.confirmation_key',
                ['uid', 'confirmation_key']
            )->join(
                ['u' => (new Model_Users())->info(Model_Users::NAME)],
                'u.uid = ui.uid',
                ['email']
            )->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )->join(
                ['c' => (new Model_Consultations())->info(Model_Consultations::NAME)],
                'c.kid = q.kid',
                ['kid', 'inp_to']
            )->where('i.user_conf = ?', 'u')->where('i.confirmation_key IS NOT NULL');

        if ($reminders !== null) {
            $select->where('reminders_sent = ?', $reminders);
        }

        foreach ($where as $cond => $parameter) {
            $select->where($cond, $parameter);
        }

        return $this->fetchAll($select);
    }

    /**
     * @param $kid
     * @return string
     * @throws \Zend_Db_Table_Exception
     */
    public function getCountContributionsByConsultation($kid)
    {
        $select = $this->selectCountContributionsByConsultation($kid);

        return $this->fetchAll($select)->current()->count;
    }

    /**
     * @param \Zend_Db_Select $contributions
     * @return int
     */
    public function getCountContributionsConfirmed(\Zend_Db_Select $contributions)
    {
        $contributions->where('user_conf LIKE ?', 'c');

        return $this->fetchAll($contributions)->current()->count;
    }

    /**
     * @param \Zend_Db_Select $contributions
     * @return int
     */
    public function getCountContributionsUnconfirmed(\Zend_Db_Select $contributions)
    {
        $contributions->where('user_conf NOT LIKE ?', 'c');

        return $this->fetchAll($contributions)->current()->count;
    }

    /**
     * @param \Zend_Db_Select $contributions
     * @return int
     */
    public function getCountContributionsBlocked(\Zend_Db_Select $contributions)
    {
        $contributions->where('block = ?', 'y');

        return $this->fetchAll($contributions)->current()->count;
    }

    /**
     * @param \Zend_Db_Select $contributions
     * @return int
     */
    public function getCountContributionsVotable(\Zend_Db_Select $contributions)
    {
        $db = $this->getDefaultAdapter();
        $contributions->where(
            $db->quoteInto('vot = ?', 'y') . ' OR ' . $db->quoteInto('vot = ?', 'u')
        );

        return $this->fetchAll($contributions)->current()->count;
    }

    /**
     * @param int $qid
     * @return \Zend_Db_Select
     */
    public function selectCountContributionsByQuestion($qid)
    {
        return $this->select()
            ->from($this, array(new Zend_Db_Expr('COUNT(*) as count')))
            ->where('qi = ?', $qid);
    }
    
    /**
     * @param int $kid
     * @return \Zend_Db_Select
     * @throws \Zend_Db_Table_Exception
     */
    public function selectCountContributionsByConsultation($kid)
    {
        return $this->select()
            ->from(['i' => $this->info(self::NAME)], [new Zend_Db_Expr('COUNT(*) as count')])
            ->setIntegrityCheck(false)
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )
            ->where('kid = ?', $kid);
    }
}
