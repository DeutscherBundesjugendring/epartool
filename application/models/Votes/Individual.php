<?php
/**
 * Votes_Individual
 * @author    Jan Suchandt, Markus Hackel
 */
class Model_Votes_Individual extends Dbjr_Db_Table_Abstract
{
    const ERROR_INVALID_CONFIRMATION_HASH = 'invalid_confirmation_hash';

    protected $_name = 'vt_indiv';
    protected $_primary = ['uid', 'tid', 'sub_uid'];

    private $allowedStatus = [
        Service_Voting::STATUS_VOTED,
        Service_Voting::STATUS_SKIPPED,
        Service_Voting::STATUS_CONFIRMED,
    ];

    /**
     * get the last vote of an subuser
     * important for back-function of voting
     * @param  string   $subuid (md5-hash)
     * @return array();
     */
    public function getLastVoteBySubuser($subuid)
    {
        if (empty($subuid)) {
            return array();
        }

        $select = $this->select();
        $select->where('sub_uid LIKE ?', $subuid);
        $select->where('status = ?', Service_Voting::STATUS_VOTED);
        $select->order('upd DESC');
        $select->limit(1);

        $row = $this->fetchRow($select);
        if (!$row) {
            return $row->toArray();
        } else {
            return array();
        }
    }

    /**
     * checks if a subuser has allready votet a thesis
     * @param  integer $tid
     * @param  string  $subuid (md5-hash)
     * @return boolean
     */
    public function allreadyVoted($tid, $subuid)
    {
        if (empty($subuid) || empty($tid)) {
            return false;
        }
        $select = $this->select();
        $select->from(
            $this,
            array(new Zend_Db_Expr('COUNT(*) as count'))
        );
        $select->where('sub_uid=?', $subuid);
        $select->where('tid=?', $tid);

        $row = $this->fetchAll($select)->current();
        if ($row->count >0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $tid
     * @param $subUid
     * @param $uid
     * @param $pts
     * @param $confirmationHash
     * @param null $particular
     * @return array|bool
     */
    public function updateVote($tid, $subUid, $uid, $pts, $confirmationHash, $particular = null)
    {
        if (!(new Service_Voting())
            ->isPointsInValidRange((new Model_Inputs())->getConsultationIdByContribution($tid), $pts)) {
            return false;
        }

        if (!is_null($particular)) { //set points and flag for superbutton
            $pts = $particular;
            $pimp = true;
        } else {
            $pimp = false;
        }
        // check if user has allready votet by this thesis
        if ($this->allreadyVoted($tid, $subUid)) {
            // Update vote
            $select = $this->select();
            $select->where('tid = ?', $tid);
            $select->where('confirmation_hash = ?', $confirmationHash);
            $select->where('sub_uid = ?', $subUid);

            $row = $this->fetchRow($select);
            if (!$row) {
                $select = $this->select();
                $select->where('tid = ?', $tid);
                $select->where('sub_uid = ?', $subUid);
                if ($this->fetchRow($select)) {
                    return ['error' => self::ERROR_INVALID_CONFIRMATION_HASH];
                }

                return false;
            }
            $row['pts'] = $pts;
            $row['is_pimp'] = (int) $pimp;
            $row['upd'] = new Zend_Db_Expr('NOW()');

            if ($row->save()) {
                return [
                    'points' => $row['pts'],
                    'is_pimp' => $row['is_pimp'],
                    'status' => $row['status'],
                ];

            } else {
                return false;
            }
        } else {
            $row = $this->createRow([
                'uid' => $uid,
                'tid' => $tid,
                'sub_uid' => $subUid,
                'pts' => $pts,
                'is_pimp' => (int) $pimp,
                'status' => Service_Voting::STATUS_VOTED,
                'confirmation_hash' => $confirmationHash,
                'upd' =>new Zend_Db_Expr('NOW()'),
            ]);
            $row->save();
            if ($row) {
                return [
                    'points' => $row['pts'],
                    'is_pimp' => $row['is_pimp'],
                    'status' => $row['status'],
                ];
            }

            return false;
        }

        return false;
    }

    /**
     * Returns array of voting values
     * @param  integer                 $tid
     * @param  integer                 $kid
     * @throws Zend_Validate_Exception
     * @return array                   Array of voting values array(
     *     'points' => $points,
     *     'cast' => $cast,
     *     'rank' => $rank,
     *    );
     */
    public function getVotingValuesByThesis($tid, $kid)
    {
        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($tid)) {
            throw new Zend_Validate_Exception('Given parameter tid must be integer!');
        }
        if (!$intVal->isValid($kid)) {
            throw new Zend_Validate_Exception('Given parameter kid must be integer!');
        }

        $points = 0;
        $rank = 0;

        $db = $this->getAdapter();
        $indiv_votes = $db->query(
            $db->select()->from(['vi' => $this->_name])
                ->join(
                    ['i' => (new Model_Inputs())->info(Model_Inputs::NAME)],
                    'i.tid = vi.tid',
                    []
                )
                ->join(
                    ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                    'q.qi = i.qi',
                    []
                )
                ->join(
                    ['c' => (new Model_Consultations())->info(Model_Consultations::NAME)],
                    'c.kid = q.kid',
                    ['kid']
                )
                ->join(
                    ['vg' => (new Model_Votes_Groups())->info(Model_Votes_Groups::NAME)],
                    'vi.sub_uid = vg.sub_uid AND vi.uid = vg.uid AND c.kid = vg.kid',
                    ['is_member']
                )
                ->where('vi.tid = ?', $tid)
                ->where('vg.is_member = ?', true)
                ->where('vi.status = ?', Service_Voting::STATUS_CONFIRMED)
                ->where('vi.pts IS NOT NULL')
        )->fetchAll();
        $cast = count($indiv_votes);

        if ($cast > 0) {
            foreach ($indiv_votes as $indiv_vote) {
                $countIndivByUid = $this->fetchRow(
                    $this->select()->from($this->_name, new Zend_Db_Expr('COUNT(*) AS count'))
                        ->where('tid = ?', $tid)
                        ->where('pts IS NOT NULL')
                        ->where('status = ?', Service_Voting::STATUS_CONFIRMED)
                        ->where('uid = ?', $indiv_vote['uid'])
                );
                $votesRights = (new Model_Votes_Rights())->find($kid, $indiv_vote['uid'])->current();
                $indiv_points = ($votesRights['vt_weight']/$countIndivByUid['count']) * $indiv_vote['pts'];

                $points += $indiv_points;
            }

            $rank = $points / $cast;
        }

        return array(
            'points' => $points,
            'cast' => $cast,
            'rank' => (string) $rank,
        );
    }

    /**
     * Returns count of individual votes by consultation
     * @param  integer                 $kid
     * @throws Zend_Validate_Exception
     * @return integer
     */
    public function getCountByConsultation($kid)
    {
        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($kid)) {
            throw new Zend_Validate_Exception('Given parameter kid must be integer!');
        }
        $db = $this->getAdapter();
        $select = $db
            ->select()
            ->from(array('vi' => $this->_name), new Zend_Db_Expr('COUNT(*) AS count'))
            ->join(array('i' => 'inpt'), 'vi.tid = i.tid', array())
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )
            ->where('q.kid = ?', $kid)
            ->where('vi.pts IS NOT NULL');
        $stmt = $db->query($select);

        return $stmt->fetchColumn();
    }

    /**
     * Return the last tid of a subuser
     * @param  string $subUid (md5-hash)
     * @return array
     */
    public function getLastBySubuser($subUid)
    {
        $result = array();

        $db = $this->getAdapter();
        $select = $db->select();
        $select->from(array('vi' => 'vt_indiv'));
        $select->joinLeft(array('i' => 'inpt'), 'vi.tid = i.tid');
        $select->where('sub_uid = ?', $subUid);
        $select->order('upd DESC');
        $select->limit(1);

        $stmt = $db->query($select);
        $row = $stmt->fetchAll();

        if ($row) {
            $result = $row;
        }

        return $result;
    }

    /**
     * Returns the count of voted thesis of a subuser
     * @param  string  $subUid (md5-hash)
     * @return integer
     */
    public function countVotesBySubuser($subUid)
    {
        $result = 0;

        if (empty($subUid)) {
            return $result;
        }

        $select = $this->select()
            ->from($this, array(new Zend_Db_Expr('COUNT(*) as count')))
            ->where('sub_uid = ?', $subUid);
        $row = $this->fetchAll($select)->current();
        if ($row) {
            return $row->count;
        }
    }

    /**
     * Update status of vote of a subuser
     * @param string $hash
     * @param string  $status       (v = voted, s = skipped, c = confirmed)
     * @param string  $statusBefore only by votes with special status (v = voted, s = skipped, c = confirmed)
     * @return bool
     */
    public function setStatusForSubuser($hash, $status, $statusBefore = '')
    {
        if (empty($hash) || empty($status) || !in_array($status, $this->allowedStatus)) {
            return false;
        }

        $db = $this->getAdapter();

        $data = ['status' => $status, 'upd' => new Zend_Db_Expr('NOW()')];
        if ($status === Service_Voting::STATUS_CONFIRMED) {
            $data['confirmation_hash'] = null;
        }
        $where = ['confirmation_hash = ?' => $hash];
        if (in_array($statusBefore, $this->allowedStatus)) {
            $where['status = ?'] = $statusBefore;
        }

        return (bool) $db->update($this->_name, $data, $where);
    }

    /**
     * @param int $uid
     * @param string $subuid
     * @param string $status
     * @return int
     */
    public function deleteByStatusForSubuser($uid, $subuid, $status)
    {
        return $this->delete([
            'uid = ?' => $uid,
            'sub_uid = ?' => $subuid,
            'status = ?' => $status,
        ]);
    }

     /* gets the superbutton thesis  */
    public function getParticularImportantVote($subUid)
    {
        $db = $this->getAdapter();
        $select = $db->select();
        $select->from(['vi' => 'vt_indiv']);
        $select->joinLeft(['i' => 'inpt'], 'vi.tid = i.tid');
        $select->where('sub_uid = ?', $subUid);
        $select->where('is_pimp = ?', true);
        $select->order('upd DESC');

        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        return $row;
    }

    /**
     * @param $subUid
     * @return int
     */
    public function countParticularImportantVote($subUid)
    {
        $rowset = $this->fetchAll(
            $this->select()
                ->where('sub_uid = ?', $subUid)
                ->where('is_pimp = ?', true)
        );
        $rowCount = count($rowset);

        return $rowCount;
    }

    /**
     * Delete the superbutton thesis from basket
     * @author Karsten Tackmann
     */
     public function deleteParticularImportantVote($uid,$subUid, $tid)
    {
        if (empty($uid) || empty($subUid) || empty($tid)) {
            return false;
        }
        $db = $this->getAdapter();
        $where = array(
            'uid = ?'    => $uid,
            'sub_uid = ?' => $subUid,
            'tid = ?' => $tid,
        );
        $result = $db->delete($this->_name, $where);
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * @param $tid
     * @param $subUid
     * @param $uid
     * @param $maxpoints
     * @param $factor
     * @param $max
     * @param $confirmationHash
     * @return array|bool
     */
    public function updateParticularImportantVote($tid, $subUid, $uid, $maxpoints, $factor, $max, $confirmationHash)
    {
        if ($this->countParticularImportantVote($subUid) < $max) {
            $particular = $maxpoints * $factor;
            $updateVoteSuccess = $this->updateVote($tid, $subUid, $uid, 0, $confirmationHash, $particular);

            if (is_array($updateVoteSuccess) && isset($updateVoteSuccess['error'])) {
                return $updateVoteSuccess;
            }

            if ($updateVoteSuccess) {
                return array(
                        'points' => $updateVoteSuccess['points'],
                        'is_pimp' => $updateVoteSuccess['is_pimp']
                        );
            } else {
                return false;
            }

        } else {
            return    array('max' =>$max);
        }
    }

    /**
     * @param $tid
     * @param $subuid
     * @return bool|null|\Zend_Db_Table_Row_Abstract
     */
    public function getCurrentVote($tid, $subuid)
    {
        if (empty($subuid) || empty($tid)) {
            return false;
        }

        $select = $this->select();
        $select->where('sub_uid = ?', $subuid);
        $select->where('tid = ?', $tid);
        $row = $this->fetchRow($select);

        return $row;

    }

    /**
     * Get All votes from one user
     * @see VotingController |admin:participanteditAction()
     * @param string $subuid       (md5-hash)
     * @return Zend_Db_Table_Rowset_Abstract
     * @throws Zend_Validate_Exception
     */
    public function getUservotes($subuid)
    {

        $alnumVal = new Zend_Validate_Alnum();
        if (!$alnumVal->isValid($subuid)) {
            throw new Zend_Validate_Exception('Given parameter sub_uid must be alphanumerical! getUservotes');
        }

        $select = $this->select();
        $select->where('sub_uid = ?', $subuid);
        $select->where('sub_uid = ?', $subuid);
        $select->order('tid ASC');
        $result = $this->fetchAll($select);
        return $result;
    }

    /**
     * Delet all votes from one user
     * @see VotingController |admin:participanteditAction()
     * @param string $subuid       (md5-hash)
     * @return bool
     * @throws Zend_Validate_Exception
     */
    public function deleteUservotes($subuid)
    {

        $alnumVal = new Zend_Validate_Alnum();
        if (!$alnumVal->isValid($subuid)) {
            throw new Zend_Validate_Exception('Given parameter sub_uid must be alphanumerical!deleteUservotes');
        }

        $db = $this->getAdapter();
        $where = array(
            'sub_uid = ?' => $subuid,
        );
        $result = $db->delete($this->_name, $where);
        if ($result) {
            return true;
        }
        return false;
    }


    /**
     * Restore votes for origin user
     * @see VotingController |admin:participanteditAction()
     * @param string $subuid       (md5-hash)
     * @throws Zend_Validate_Exception
     */
    public function insertMergedUservotes($subuid, $values)
    {

        $alnumVal = new Zend_Validate_Alnum();
        if (!$alnumVal->isValid($subuid)) {
            throw new Zend_Validate_Exception('Given parameter sub_uid must be alphanumerical!');
        }

            $data = array(
                'uid' => $values['uid'],
                'tid' => $values['tid'],
                'sub_uid' => $subuid,
                'pts' => $values['pts'],
                'is_pimp' => $values['is_pimp'],
                'status'=> Service_Voting::STATUS_VOTED,
                'upd' =>new Zend_Db_Expr('NOW()')
            );
            $row = $this->createRow($data);
            $row->save();
    }

    /**
     * @param $confirmationHash
     * @return mixed
     * @throws \Zend_Db_Table_Exception
     */
    public function getByConfirmationHash($confirmationHash)
    {
        $db = $this->getAdapter();
        $select = $db
            ->select()
            ->from(['vi' => $this->_name])
            ->join(
                ['vg' => (new Model_Votes_Groups())->info(Model_Votes_Groups::NAME)],
                'vi.sub_uid = vg.sub_uid',
                ['sub_uid', 'sub_user', 'is_member']
            )
            ->join(
                ['c' => (new Model_Consultations())->info(Model_Consultations::NAME)],
                'vg.kid = c.kid',
                ['titl', 'titl_short', 'kid']
            )
            ->join(
                ['u' => (new Model_Users())->info(Model_Users::NAME)],
                'vi.uid = u.uid',
                ['uid', 'email']
            )->join(
                ['vr' => (new Model_Votes_Rights())->info(Model_Votes_Rights::NAME)],
                'vi.uid = vr.uid AND vg.kid = vr.kid',
                ['vt_code']
            )
            ->where('vi.confirmation_hash = ?', $confirmationHash)
            ->group(['vg.sub_uid', 'vi.confirmation_hash']);

        return $db->query($select)->fetch();
    }

    /**
     * @param int $kid
     * @return \Zend_Db_Select
     */
    public function selectVotesGroupsByConsultation($kid)
    {
        return $this->select()
            ->from(['vti' => $this->_name], ['uid'])
            ->join(['i' => (new Model_Inputs())->getName()], 'vti.tid = i.tid', [])
            ->join(['q' => (new Model_Questions())->getName()], 'i.qi = q.qi', [])
            ->where('q.kid = ?', $kid)
            ->group('vti.uid');
    }

    /**
     * @param string $confirmationHash
     * @return array
     * @throws \Zend_Db_Table_Exception
     */
    public function getOneVoteWithDependencies($confirmationHash)
    {
        $q = $this->select()
            ->from(['v' => $this->info(self::NAME)])
            ->setIntegrityCheck(false)
            ->join(
                ['i' => (new Model_Inputs())->info(Model_Inputs::NAME)],
                'i.tid = v.tid',
                []
            )->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )->join(
                ['c' => (new Model_Consultations())->info(Model_Consultations::NAME)],
                'c.kid = q.kid',
                ['kid', 'titl', 'titl_short']
            )
            ->where('confirmation_hash = ?', $confirmationHash);

        $result = $this->fetchAll($q, null, 1)->current();

        if ($result) {
            return $result->toArray();
        }

        return [];
    }

    /**
     * @param $where
     * @return array
     * @throws \Zend_Db_Table_Exception
     */
    public function getUnconfirmedVotesWithDependencies($where)
    {
        $q = $this->select()
            ->from(['v' => $this->info(self::NAME)])
            ->setIntegrityCheck(false)
            ->join(
                ['i' => (new Model_Inputs())->info(Model_Inputs::NAME)],
                'i.tid = v.tid',
                []
            )
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )
            ->join(
                ['c' => (new Model_Consultations())->info(Model_Consultations::NAME)],
                'c.kid = q.kid',
                ['kid', 'titl', 'titl_short', 'vot_to']
            )
            ->join(
                ['vg' => (new Model_Votes_Groups())->info(Model_Votes_Groups::NAME)],
                'v.sub_uid = vg.sub_uid AND v.uid = vg.uid AND c.kid = vg.kid',
                ['sub_user', 'is_member', 'reminders_sent']
            )
            ->where('status = ?', Service_Voting::STATUS_VOTED);

        foreach ($where as $cond => $val) {
            $q->where($cond, $val);
        }

        $q
            ->group('v.sub_uid')
            ->order('v.upd DESC');

        return $this->fetchAll($q);
    }
}
