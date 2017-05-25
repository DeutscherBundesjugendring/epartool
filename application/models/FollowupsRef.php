<?php

class Model_FollowupsRef extends Zend_Db_Table_Abstract
{
    protected $_name = 'fowups_rid';
    protected $_primary = ['id'];

    protected $_referenceMap = array(
        'Followups_Ref' => array(
            'columns' => 'fid_ref', 'refTableClass' => 'Model_Followups', 'refColumns' => 'fid',
            'onDelete' => self::CASCADE,
            'onUpdate' => self::CASCADE
        ),
        'Followups' => array(
            'columns' => 'fid', 'refTableClass' => 'Model_Followups', 'refColumns' => 'fid',
            'onDelete' => self::CASCADE,
            'onUpdate' => self::CASCADE
        ),
        'FollowupFiles' => array(
            'columns' => 'ffid', 'refTableClass' => 'Model_FollowupFiles', 'refColumns' => 'ffid',
            'onDelete' => self::CASCADE,
            'onUpdate' => self::CASCADE
        ),
        'Inputs' => array(
            'columns' => 'tid', 'refTableClass' => 'Model_Inputs', 'refColumns' => 'tid',
            'onDelete' => self::CASCADE,
            'onUpdate' => self::CASCADE
        ),
    );

    /**
     * Resets a link between snippet and any of the following
     * - snippet
     * - follow-up
     * - input
     * @param  array   $ids    An array of linked entity ids
     * @param  integer $fid    The id of the snipped being linked
     * @param  string  $type   Identifies the target entity type. Takes values
     *                         - tid (input)
     *                         - fid (snippet)
     *                         - ffid (followup)
     * @return integer        The number of rows inserted.
     */
    public function insertBulk($ids, $fid, $type)
    {
        $inserted = 0;
        $this->delete(['fid_ref = ?' => $fid, $this->getAdapter()->quoteIdentifier($type) . 'IS NOT NULL']);
        foreach ($ids as $id) {
            $data = ['fid_ref' => $fid, $type => $id];
            $inserted++;
            $this->insert($data);
        }

        return $inserted;
    }

    /**
    * getFollowupCountByFids
    * get the reference count of the fids in a given array
    * @param array $fidArray
    * @param string $where
    * @return array
    */
    public function getFollowupCountByFids($fidArray, $where = null)
    {
        if (count($fidArray) == 0) {
            return [];
        }

        $select = $this->select();
        $select->from($this, ['fid_ref', 'count' => new Zend_Db_Expr('count(*)')]);
        $select->where('fid_ref IN(?)', $fidArray);
        if ($where) {
            $select->where($where);
        }
        $select->group(['fid_ref']);
        $counts = $this->fetchAll($select)->toArray();
        $result = [];
        foreach ($counts as $count) {
            $result[$count['fid_ref']] = $count['count'];
        }

        return $result;
    }

    /**
    * getFollowupCountByTids
    * get the reference count of the tids in a given array
    * @param array $tidArray
    * @param string $where
    * @return array
    */
    public function getFollowupCountByTids($tidArray, $where = null)
    {
        if (count($tidArray) == 0) {
            return [];
        }

        $select = $this->select();
        $select->from($this, ['tid', 'count' => new Zend_Db_Expr("count(*)")]);
        $select->where('tid IN(?)', $tidArray);
        if ($where) {
            $select->where($where);
        }
        $select->group(['tid']);
        $counts = $this->fetchAll($select)->toArray();
        $result = [];
        foreach ($counts as $count) {
            $result[$count['tid']]    = $count['count'];
        }

        return $result;
    }

    public function getRelatedFollowupByFid($fid)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($fid)) {
                    throw new Zend_Exception('Given fid must be integer!');
        }
        $select = $this->select();
        $select->from($this, ['fid_ref']);
        $select->where('fid=?', $fid);

        $result = $this->fetchAll($select)->toArray();

        return $result;
    }

    /**
     * @param int $fid
     * @return int
     */
    public function getRelatedFollowupCountByFid($fid)
    {
        $select = $this->select()
            ->from(['f' => $this->info(self::NAME)], [new Zend_Db_Expr('COUNT(*) as count')])
            ->where('f.fid = ?', (int) $fid);

        return (int) $this->fetchAll($select)->current()->count;
    }

    public function getRelatedInputsByFid($fid)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($fid)) {
                    throw new Zend_Exception('Given fid must be integer!');
        }
        $select = $this->select();
        $select->from($this, ['tid']);
        $select->where('fid_ref=?', $fid);
        $select->where('tid<>?', 0);

        $result = $this->fetchAll($select)->toArray();

        return $result;
    }

    public function deleteRef($fidRef, $reftype, $refid)
    {
        $db = $this->getDefaultAdapter();
        $where = $db->quoteInto('fid_ref = ?', $fidRef) . ' AND ' .  $db->quoteInto($reftype . ' = ?', $refid);
        $this->delete($where);
    }
}
