<?php
/**
 * Votes_Groups
 * @author Markus Hackel
 *
 */
class Model_Votes_Groups extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'vt_grps';
    protected $_primary = array(
        'uid', 'sub_uid', 'kid'
    );

    /**
     * Returns all groups by consultation
     *
     * @param  integer                 $kid
     * @throws Zend_Validate_Exception
     * @return array
     */
    public function getByConsultation($kid, $uid=0)
    {
        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($kid)) {
            throw new Zend_Validate_Exception('Given parameter kid must be integer!');
        }
         if (!$intVal->isValid($uid)) {
            throw new Zend_Validate_Exception('Given parameter uid must be integer!');
        }

        $select = $this
            ->getDefaultAdapter()
            ->select()
            ->from(array('vg' => $this->_name), array('*'))
            ->joinLeft(array('u' => 'users'), 'u.uid = vg.uid', array('*'))
            ->joinLeft(
                ['vt_indiv' => 'vt_indiv'],
                'vg.sub_uid =vt_indiv.sub_uid ',
                [new Zend_Db_Expr('COUNT(vt_indiv.sub_uid) as count')]
            )
            ->where('vg.kid = ?', $kid)
            ->group('vg.sub_uid')
            ->order('u.email');
        if ($uid != 0) {
            $select->where('vg.uid = ?', $uid);
        }
        $res = $select
            ->query()
            ->fetchAll();

        return $res;
    }

    /**
     * Sets field 'member' to 'n' by given key
     *
     * @param  integer                 $kid
     * @param  integer                 $uid
     * @param  string                  $sub_uid
     * @throws Zend_Validate_Exception
     * @return boolean
     */
    public function denyVoter($kid, $uid, $sub_uid)
    {
        $intVal = new Zend_Validate_Int();
        $alnumVal = new Zend_Validate_Alnum();
        if (!$intVal->isValid($kid)) {
            throw new Zend_Validate_Exception('Given parameter kid must be integer!');
        }
        if (!$intVal->isValid($uid)) {
            throw new Zend_Validate_Exception('Given parameter uid must be integer!');
        }
        if (!$alnumVal->isValid($sub_uid)) {
            throw new Zend_Validate_Exception('Given parameter sub_uid must be alphanumerical!');
        }

        $row = $this->find($uid, $sub_uid, $kid)->current();
        if ($row) {
            $row->member = 'n';
            $row->save();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Sets field 'member' to 'y' by given key
     *
     * @param  integer                 $kid
     * @param  integer                 $uid
     * @param  string                  $subUid
     * @throws Zend_Validate_Exception
     * @return boolean
     */
    public function confirmVoter($kid, $uid, $subUid)
    {
        $row = $this->find($uid, $subUid, $kid)->current();
        if ($row) {
            $row->member = 'y';
            return $row->save();
        }

        return false;
    }

    /**
     * Deletes rows by key and depending rows in vt_indiv
     *
     * @param  integer                 $kid
     * @param  integer                 $uid
     * @param  string                  $sub_uid
     * @throws Zend_Validate_Exception
     * @return integer                 Number of rows deleted
     */
    public function deleteVoter($kid, $uid, $sub_uid)
    {
        $intVal = new Zend_Validate_Int();
        $alnumVal = new Zend_Validate_Alnum();
        if (!$intVal->isValid($kid)) {
            throw new Zend_Validate_Exception('Given parameter kid must be integer!');
        }
        if (!$intVal->isValid($uid)) {
            throw new Zend_Validate_Exception('Given parameter uid must be integer!');
        }
        if (!$alnumVal->isValid($sub_uid)) {
            throw new Zend_Validate_Exception('Given parameter sub_uid must be alphanumerical!');
        }

        $votesIndivModel = new Model_Votes_Individual();
        $votesIndivModel->delete(array(
            $votesIndivModel->getAdapter()->quoteInto('uid = ?', $uid),
            $votesIndivModel->getAdapter()->quoteInto('sub_uid = ?', $sub_uid),
        ));

        $nr = $this->delete(array(
            $this->getAdapter()->quoteInto('uid = ?', $uid),
            $this->getAdapter()->quoteInto('sub_uid = ?', $sub_uid),
            $this->getAdapter()->quoteInto('kid = ?', $kid),
        ));

        return $nr;
    }

    /**
     * Liefert einen Subuser
     * @param  string  $email
     * @param  integer $uid
     * @param  integer $kid
     * @return array   (subuser-entry/empty array)
     */
    public function getByEmail($email, $uid, $kid)
    {
        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($kid) || !$intVal->isValid($uid)) {
            throw new Zend_Validate_Exception('Given parameter kid/uid must be integer!');
        }
        if (empty($email)) {
            throw new Zend_Validate_Exception('Given parameter $email is empty!');
        }
        $select = $this->select();
        $select->where('uid = ?', $uid);
        $select->where('kid = ?', $kid);
        $select->where('sub_user = ?', $email);

        $result = $this->fetchRow($select);
        if ($result) {
            return $result->toArray();
        } else {
            return array();
        }

    }

    /**
     * get the list of inputs, the user have to vote
     * @param  string  $sub_uid
     * @param  integer $kid
     * @return array
     */
    public function getVotingList($sub_uid, $kid)
    {
        if (empty($sub_uid) || empty($kid)) {
            return array();
        }

        $select = $this->select();
        $select->where('kid = ?', $kid);
        $select->where('sub_uid = ?', $sub_uid);

        $row = $this->fetchRow($select);
        if ($row) {
            $tids = explode(',',$row->vt_inp_list);
            $qids = explode(',',$row->vt_rel_qid);
            $result = array();

            foreach ($tids AS $key=>$val) {
                $result[$val] = $qids[$key];
            }

            return $result;
        } else {
            return array();
        }
    }

    /**
     * get the list of votable thesis of a question
     * @param  integer $kid
     * @param  string  $sub_uid Hash of subuser
     * @param  integer $qid
     * @return array
     */
    public function getVotingListByQuestion($kid, $sub_uid, $qid)
    {
        if (empty($sub_uid) || empty($kid) || empty($qid)) {
            return array();
        }
        // first get the list of tids that are votable from subuser
        $thesis2voteTmp = $this->getVotingList($sub_uid, $kid);
        // remove all thesis that are not this question
        $thesis2vote = array();
        // $key (tid) $val (qid)
        foreach ($thesis2voteTmp AS $key=>$val) {
            if ($val == $qid) {
                $thesis2vote[] = $key;
            }
        }

        return $thesis2vote;
    }

    /**
     * get the list of votable thesis of a tag
     * @param  integer $kid
     * @param  string  $sub_uid Hash of subuser
     * @param  integer $qid
     * @return array
     */
    public function getVotingListByTag($kid, $sub_uid, $tagId)
    {
        if (empty($sub_uid) || empty($kid) || empty($tagId)) {
            return array();
        }
        // first get the list of tids that are votable from subuser
        $thesis2voteTmp = $this->getVotingList($sub_uid, $kid);
        // remove all thesis that are not this question
        $thesis2vote = array();

        $inputModel = new Model_Inputs();
        $thesisByTag = $inputModel->getThesisbyTag($kid, $tagId);
        foreach ($thesisByTag AS $val) {
            if (array_key_exists($val['tid'], $thesis2voteTmp)) {
                $thesis2vote[] = $val['tid'];
            }
        }

        return $thesis2vote;
    }

    /**
     * add
     * @desc add new entry to db-table
     * @name add
     * @param  array   $data
     * @return integer primary key of inserted entry
     *
     */
    public function add($data)
    {
        $row = $this->createRow($data);

        return (int) $row->save();
    }

    /**
     * Exclude a tid from the votingchain of a group
     * @param  integer $kid
     * @param  string  $subUid
     * @param  integer $tid
     * @return boolean
     */
    public function excludeThesisFromVotingchain($kid, $subUid, $tid)
    {
        if (empty($kid) || empty($subUid) || empty($tid)) {
            return array();
        }

        // Get current votingchain ([tid] = qid)
        $votingList = $this->getVotingList($subUid, $kid);
        $votingListNewTid = array();
        $votingListNewQid = array();
        foreach ($votingList AS $ttid => $qid) {
            if ($tid != $ttid) {
                $votingListNewTid[] = $ttid;
                $votingListNewQid[] = $qid;
            }
        }

        $select = $this->select();
        $select->where('kid = ?', $kid);
        $select->where('sub_uid = ?', $subUid);

        $result = $this->fetchRow($select);
        if ($result) {
            $result->vt_inp_list = implode(',', $votingListNewTid);
            $result->vt_rel_qid = implode(',', $votingListNewQid);
            $result->save();

            return true;
        } else {
            return false;
        }

    }

    /**
     * returns group by user and subuser
     * @param integer $uid
     * @param string  $subUid
     * return array
     */
    public function getByUser($uid, $subUid)
    {
        if (empty($uid) || empty($subUid)) {
            return array();
        }

        $select = $this->select();
        $select->where('uid = ?', $uid);
        $select->where('sub_uid LIKE ?', $subUid);
        $row = $this->fetchRow($select);

        if (!$row) {
            return array();
        } else {
            return $row->toArray();
        }
    }


    /**
     * Returns all users by consultation
     *
     * @param  integer                 $kid
     * @throws Zend_Validate_Exception
     * @return array
     */
    public function getUserByConsultation($kid)
    {
        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($kid)) {
            throw new Zend_Validate_Exception('Given parameter kid must be integer!');
        }

        $select = $this->select()
            ->where('kid = ?', $kid)
            ->order('sub_user', 'ASC');
        $result = $this->fetchAll($select);
        return $result;
    }

    /**
     * Delete Voter
     *
     * @param  alnum subuid
     * @see VotingController |admin:participanteditAction()
     * @throws Zend_Validate_Exception
     * @return boolean
     */
    public function deleteVoterBySubUid($subuid)
    {
        $alnumVal = new Zend_Validate_Alnum();
        if (!$alnumVal->isValid($subuid)) {
            throw new Zend_Validate_Exception('Given parameter sub_uid must be alphanumerical!');
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
}
