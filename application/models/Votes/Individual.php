<?php
/**
 * Votes_Individual
 * @author    Jan Suchandt, Markus Hackel
 */
class Model_Votes_Individual extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'vt_indiv';

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
        $select->where('status = ?', 'v');
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

    public function updateVote($tid, $subUid, $uid, $pts, $particular = null)
    {
        if ($pts < 0 || $pts > 5) {
            return false;
        }

        $pimp = 'n';
        if ($particular) { //set points and flag for super button
            $pts = $particular;
            $pimp ='y';
        }

        // check if user has allready voted on this thesis
        if ($this->allreadyVoted($tid, $subUid)) {
            $select = $this
                ->select()
                ->where('tid = ?', $tid)
                ->where('sub_uid = ?', $subUid);

            $row = $this->fetchRow($select);
            $row->pts = $pts;
            $row->pimp = $pimp;
            $row->upd = new Zend_Db_Expr('NOW()');
        } else {
            $row = $this->createRow([
                'uid' => $uid,
                'tid' => $tid,
                'sub_uid' => $subUid,
                'pts' => $pts,
                'pimp' => $pimp,
                'status'=>'v',
                'upd' =>new Zend_Db_Expr('NOW()')
            ]);
        }

        if ($row->save()) {
            return [
                'points' => $row->pts,
                'pimp' => $row->pimp,
            ];
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

        $indiv_votes = $this->fetchAll(
            $this->select()
                ->where('tid = ?', $tid)
                ->where('pts < ?', 4)
        );
        $cast = count($indiv_votes);

        if ($cast > 0) {
            foreach ($indiv_votes as $indiv_vote) {
                $countIndivByUid = $this->fetchRow(
                    $this->select()->from($this->_name, new Zend_Db_Expr('COUNT(*) AS count'))
                        ->where('tid = ?', $tid)
                        ->where('pts < ?', 4)
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
            ->where('vi.pts < ?', 4);
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
     * @param integer $uid
     * @param string  $subuid       (md5-hash)
     * @param string  $status       (v = voted, s = skipped, c = confirmed)
     * @param string  $statusBefore only by votes with special status (v = voted, s = skipped, c = confirmed)
     */
    public function setStatusForSubuser($uid, $subuid, $status, $statusBefore = null)
    {
        $data = [
            'status' => $status,
            'upd' => new Zend_Db_Expr('NOW()')
        ];

        $where = [
            'uid = ?' => $uid,
            'sub_uid = ?' => $subuid
        ];
        if ($statusBefore) {
            $where['status = ?'] = $statusBefore;
        }

        return $this->update($data, $where);
    }

    /**
     * Delete votes for user by status
     * @param string $subuid       (md5-hash)
     * @param string $status       (v = voted, s = skipped, c = confirmed)
     * @param string $statusBefore only by votes with special status (v = voted, s = skipped, c = confirmed)
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
        $select->where('pimp = ?', 'y');
        $select->order('upd DESC');

        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        return $row;
    }

    /* counts how much user clicks on superbutton */
    public function countParticularImportantVote($subUid)
    {
        $rowset = $this->fetchAll(
            $this->select()
                ->where('sub_uid = ?', $subUid)
                ->where('pimp = ?', 'y')
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

    /* initalize update for click on superbutton */
    public function updateParticularImportantVote($tid, $subUid, $uid, $maxpoints, $factor, $max)
    {
        if ($this->countParticularImportantVote ($subUid) < $max) {

            $particular = $maxpoints * $factor;
            $updateVoteSuccess = $this->updateVote($tid, $subUid, $uid, 0, $particular);

            if ($updateVoteSuccess) {
                return array(
                        'points' => $updateVoteSuccess['points'],
                        'pimp' => $updateVoteSuccess['pimp']
                        );
            } else {
                return false;
            }

        } else {
            return    array('max' =>$max);
            }
    }

    public function getCurrentVote($tid, $subuid)
    {
        if (empty($subuid) || empty($tid)) {
            return false;
        }

        $select = $this->select();
        $select->where('sub_uid = ?', $subuid);
        $select->where('tid = ?',$tid);
        $row = $this->fetchRow($select);

        return $row;

    }

    /**
     * Get All votes from one user
     * @see VotingController |admin:participanteditAction()
     * @param string $subuid       (md5-hash)
     */
    public function getUservotes($subuid) {

        $alnumVal = new Zend_Validate_Alnum();
        if (!$alnumVal->isValid($subuid)) {
            throw new Zend_Validate_Exception('Given parameter sub_uid must be alphanumerical! getUservotes');
        }

        $select = $this->select();
        $select->where('sub_uid = ?', $subuid);
        $select->where('sub_uid = ?', $subuid);
        $select->order('tid','ASC');
        $result = $this->fetchAll($select);
        return $result;
    }

    /**
     * Delet all votes from one user
     * @see VotingController |admin:participanteditAction()
     * @param string $subuid       (md5-hash)
     */
    public function deleteUservotes($subuid) {

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
     */
    public function insertMergedUservotes($subuid, $values) {

        $alnumVal = new Zend_Validate_Alnum();
        if (!$alnumVal->isValid($subuid)) {
            throw new Zend_Validate_Exception('Given parameter sub_uid must be alphanumerical!');
        }

            $data = array(
                'uid' => $values['uid'],
                'tid' => $values['tid'],
                'sub_uid' => $subuid,
                'pts' => $values['pts'],
                'pimp' => $values['pimp'],
                'status'=>'v',
                'upd' =>new Zend_Db_Expr('NOW()')
            );
            $row = $this->createRow($data);
            $row->save();
    }
}
