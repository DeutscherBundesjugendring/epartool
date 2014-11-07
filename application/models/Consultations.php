<?php
/**
 * Consultations
 * @desc        Class of consultation
 * @author    Jan Suchandt
 */
class Model_Consultations extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'cnslt';
    protected $_primary = 'kid';

    protected $_dependentTables = array(
        'Model_Articles', 'Model_Questions', 'Model_Votes', 'Model_Votes_Rights'
    );

    /**
     * getById
     * @desc returns entry by id
     * @param  integer $id consultations-id
     * @return array
     */
    public function getById($id)
    {
        $result = array();
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return array();
        }

        // find current consultation
        $sub = array();
        $row = $this->find($id)->current();
        if (!empty($row)) {
            // find Questions
            $questionModel = new Model_Questions();
            $subrow2 = $questionModel->getByConsultation($id)->toArray();
        foreach ($subrow2 as $key => $value) {
                $sub[$value["qi"]] = $value;
        }

            $result = $row->toArray();

            $articleModel = new Model_Articles();
            $result['articles'] = $articleModel->getByConsultation($id);

            $result['questions'] = $sub;
        }

        return $result;
    }

    /**
     * add
     * @desc add new entry to db-table
     * @param  array   $data
     * @return integer primary key of inserted entry
     */
    public function add($data)
    {
        return (int) $this->insert($data);
    }

    /**
     * updateById
     * @desc update entry by id
     * @param  integer $id   consultations-id
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

        $where = $this->getDefaultAdapter()
                ->quoteInto($this->_primary[1] . '=?', $id);

        return $this->update($data, $where);
    }

    /**
     * deleteById
     * @desc delete entry by id
     * @param  integer $id consultations-id
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

        // where
        $where = $this->getDefaultAdapter()
                ->quoteInto($this->_primary[1] . '=?', $id);
        $result = $this->delete($where);

        return $result;
    }

    /**
     * returns the last consultations
     * @param  integer                       $limit count of consultations
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getLast($limit)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($limit)) {
            return array();
        }

        // fetch
        $where = array(
            'public="y"'
        );
        $order = array(
            'ord DESC'
        );
        $result = $this->fetchAll($where, $order, $limit);

        return $result;
    }

    /**
     * getVotingRights
     * @desc return the rights of
     * @param  integer $id consultations-id
     * @return array
     */
    public function getVotingRights($id)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return array();
        }
        // find current consultation
        $row = $this->find($id)->current();
        // find Voting-Rights (see model Votes/Rights.php)
        $subrow1 = $row->findVotes_Rights()->toArray();

        return $subrow1;
    }

    /**
     * getVotingResults
     * @desc return the results of
     * @param  integer $id consultations-id
     * @return array
     */
    public function getVotingResults($id)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return array();
        }
        // find current consultation
        $row = $this->find($id)->current();
        // find Voting-Rights (see model Votes/Rights.php)
        $subrow1 = $row->findVotes()->toArray();

        return $subrow1;
    }

    /**
     * Returns highest ID from database
     * @return integer
     */
    public function getLastId()
    {
        $row = $this->fetchRow(
            $this
                ->select()
                ->from($this, array(new Zend_Db_Expr('max(kid) as maxId')))
        );

        return $row->maxId;
    }

    /**
     * Returns array of entries for use as pages in Zend_Navigation
     * @return array
     */
    public function getNavigationEntries()
    {
        $entries = array();
        $select = $this->select();
        $select->order('ord DESC');
        $rowSet = $this->fetchAll($select);
        foreach ($rowSet as $row) {
            $entries[] = array(
                'label' => $row->titl,
                'module' => 'admin',
                'action' => 'index',
                'controller' => 'consultation',
                'params' => array (
                    'kid' => $row->kid
                ),
            );
        }

        return $entries;
    }

    /**
     * Returns all rows with public = 'y'
     * @return Zend_Db_Table_Rowset
     */
    public function getPublic()
    {
        $select = $this->select()->where('public = ?', 'y')->order('ord DESC');

        return $this->fetchAll($select);
    }

    /**
     * Returns entries for the teaser view helper
     *
     * @return array
     */
    public function getTeaserEntries()
    {
        $entries = array();

        $select = $this->select();
        $select->where('public = ?', 'y');
        $select->order(array('ord DESC'));
        $rowSet = $this->fetchAll($select);

        $date = new Zend_Date();
        foreach ($rowSet as $row) {
            // Berechne die Zeitabstände der einzelnen Datumsfelder zum aktuellen Zeitpunkt
            $timeDiff = array(
                'inp_fr' => Zend_Date::now()->sub($date->set($row->inp_fr))->toValue(),
                'inp_to' => Zend_Date::now()->sub($date->set($row->inp_to))->toValue(),
                'vot_fr' => Zend_Date::now()->sub($date->set($row->vot_fr))->toValue(),
                'vot_to' => Zend_Date::now()->sub($date->set($row->vot_to))->toValue(),
            );
            $relevantField = 'inp_fr';
            foreach ($timeDiff as $field => $value) {
                if ($value > 0) {
                    // relevantes Datumsfeld darf nicht in der Zukunft liegen
                    if ($value < $timeDiff[$relevantField]) {
                        // relevantes Feld ist dasjenige mit dem kleinsten positiven Abstand
                        $relevantField = $field;
                    }
                }
            }
            if ($timeDiff[$relevantField] < 0) {
                // wenn relevantes Feld in Zukunft liegt, fällt der Datensatz raus
                continue;
            }
            // Datensatz im entries Array ablegen, key ist der Rang
            $entries[$row->ord] = $row->toArray();
            $entries[$row->ord]['relevantField'] = $relevantField;
        }

        // Sortiere nach Rang in absteigender Reihenfolge
        krsort($entries);
        // Nur die ersten drei Einträge werden benötigt
        $entries = array_slice($entries, 0, 3);

        return $entries;
    }

    /**
     * Return all
     */
    public function getAll()
    {
        return $this->fetchAll($this->select()->order('ord DESC'));
    }

    /**
     * Search for inputs, questions and articles of consultations
     */
    public function search($needle)
    {
        $result = array();

        if ($needle==='') {
            return $result;
        }

        $select = $this->select();
        $select->from(
            array('c'=>'cnslt'),
            array(
                'cid'=>'kid',
                'titel'=>'titl',
                'expl'=>'LOWER(expl)'
            )
        );

        $select->order('ord DESC');
        $rows = $this->fetchAll($select);
        $i = 0;

        foreach ($rows AS $consultation) {

            $result[$i] = $consultation->toArray();
            // check if the needle is in consultation-explenation
            if (strpos($consultation->expl, htmlentities($needle))!==false) {
                $result[$i]['inExpl'] = true;
            }
            // search articles
            $articles = new Model_Articles();
            $result[$i]['articles'] = $articles->search($needle, (int) $consultation->cid);

            // search questions
            $questions = new Model_Questions();
            $result[$i]['questions'] = $questions->search($needle, $consultation->cid);
            // search questions
            $inputs = new Model_Inputs();
            $result[$i]['inputs'] = $inputs->search($needle, $consultation->cid);
            $i++;
        }

        return $result;
    }

    public function getByUser($uid)
    {
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from(
                ['i' => (new Model_Inputs())->info(Model_Inputs::NAME)],
                ['count' => 'COUNT(*)']
            )
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )
            ->join(
                ['c' => (new Model_Consultations())->info(Model_Consultations::NAME)],
                'q.kid = c.kid',
                ['titl' => 'c.titl', 'kid']
            )
            ->where('i.uid = ?', $uid)
            ->group('c.kid');

        return $this->fetchAll($select);
    }

    /**
     * Finds out if there are any participants in this consultation
     * @param  integer  $kid The consultation identificator
     * @return boolean       Indicates if there are any participants
     */
    public function hasParticipants($kid)
    {
        $inputModel = new Model_Inputs();
        $row = $inputModel->fetchRow(
            $inputModel
                ->select()
                ->setIntegrityCheck(false)
                ->from(['i' => $inputModel->info(Model_Inputs::NAME)], ['uid'])
                ->join(
                    ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                    'q.qi = i.qi',
                    []
                )
                ->where('kid=?', $kid)
        );

        return $row ? true : false;
    }

    /**
     * Finds out if there are any participants in this consultation who have subscribed to the newsletter
     * @param  integer  $kid The consultation identificator
     * @return boolean       Indicates if there are any newsletter subscribed participants
     */
    public function hasNewsletterSubscribers($kid)
    {
        $userConsultModel = new Model_User_Info();
        $row = $userConsultModel->fetchRow(
            $userConsultModel
                ->select()
                ->setIntegrityCheck(false)
                ->from(['u' => (new Model_Users())->info(Model_Users::NAME)], ['uid'])
                ->join(
                    ['ui' => (new Model_User_Info())->info(Model_User_Info::NAME)],
                    'u.uid = ui.uid',
                    []
                )
                ->where('ui.kid=?', $kid)
                ->where('u.newsl_subscr=?', 'y')
        );

        return $row ? true : false;
    }

    /**
     * Finds out if there are any participants in this consultation who have subscribed to the followups
     * @param  integer  $kid The consultation identificator
     * @return boolean       Indicates if there are any participants followup subscribed participants
     */
    public function hasFollowupSubscribers($kid)
    {
        $userConsultModel = new Model_User_Info();
        $row = $userConsultModel->fetchRow(
            $userConsultModel
                ->select()
                ->where('kid=?', $kid)
                ->where('cnslt_results=?', 'y')
        );

        return $row ? true : false;
    }

    /**
     * Finds out if there are any voters in this consultation
     * @param  integer  $kid The consultation identificator
     * @return boolean       Indicates if there are any voters
     */
    public function hasVoters($kid)
    {
        $inputModel = new Model_Inputs();
        $vtGroupModel = new Model_Votes_Groups();
        $row = $inputModel->fetchRow(
            $vtGroupModel
                ->select()
                ->from($vtGroupModel->getName(), array('uid'))
                ->where('kid=?', $kid)
        );

        return $row ? true : false;
    }

    /**
     * Returns a list of consultation with the the specified number of latest posts each
     * @param  integer $inputLimit The number of inputs for each consultation
     * @return array               An array of consultation arrays
     */
    public function getWithInputs($inputLimit)
    {
        $select = $this
            ->select()
            ->from(['c' => $this->info(self::NAME)], ['titl', 'titl_sub', 'kid'])
            ->setIntegrityCheck(false)
            ->joinLeft(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.kid = c.kid',
                ['qi']
            )
            ->order('kid DESC');

        $res = $this->fetchAll($select)->toArray();

        $consultations = [];
        foreach ($res as $consultation) {
            if (!isset($consultations[$consultation['kid']])) {
                $consultations[$consultation['kid']] = [
                    'titl' => $consultation['titl'],
                    'titl_sub' => $consultation['titl_sub'],
                    'questionIds' => [],
                ];
            }
            $consultations[$consultation['kid']]['questionIds'][] = $consultation['qi'];
        }

        $inputModel = new Model_Inputs();
        foreach ($consultations as &$consultation) {
            $consultation['inputs'] = $inputModel->fetchAll(
                $inputModel
                    ->select()
                    ->from(['i' => $inputModel->info(Model_Questions::NAME)], ['tid', 'thes', 'qi'])
                    ->where('qi IN (?)', $consultation['questionIds'])
                    ->order('when DESC')
                    ->limit($inputLimit)
            );
            unset($consultation['questionIds']);
        }

        return $consultations;
    }
}
