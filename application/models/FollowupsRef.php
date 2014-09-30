<?php

/**
 * Description of Model FollowupsRef
 *
 * @author Marco Dinnbier
 */
class Model_FollowupsRef extends Zend_Db_Table_Abstract
{
    protected $_name = 'fowups_rid';
    protected $_primary = array(
        'fid_ref', 'tid', 'ffid','fid'
    );

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
     * - followup
     * - input
     * @param  array   $array  An array of linekd entity ids
     * @param  integer $fid    The id of the snipped being linked
     * @param  string  $type   Identifies the target entity type. Takes values
     *                         - tid (input)
     *                         - fid (snippet)
     *                         - ffid (followup)
     * @return integer        The number of rows inserted.
     */
    public function insertBulk($array, $fid, $type)
    {
        $inserted = 0;
        $this->delete(['fid_ref = ?' => $fid, $this->getAdapter()->quoteIdentifier($type) . '!= 0']);
        foreach ($array as $id) {
            $data = ['fid_ref' => $fid, $type => $id];
            $inserted++;
            $this->insert($data);
        }

        return $inserted;
    }

    /**
    * getFollowupCountByFids
    * get the reference count of the fids in a given array
    * @param array $fidarray
    * @param string $where
    * @return array
    */
    public function getFollowupCountByFids($fidarray, $where = NULL)
    {
        if (count($fidarray) == 0) {
                return array();

        }

        $select = $this->select();
        $select->from($this, array("fid_ref", "count" => new Zend_Db_Expr("count(*)")));
        $select->where('fid_ref IN(?)', $fidarray);
        if ($where) {
                $select->where($where);
        }
        $select->group(array ("fid_ref"));
        //return (string) $select;
        $counts = $this->fetchAll($select)->toArray();
        $result = array();
        foreach ($counts as $count) {
                $result[$count['fid_ref']]    = $count['count'];
        }

        return $result;
    }

    /**
    * getFollowupCountByTids
    * get the reference count of the tids in a given array
    * @param array $tidarray
    * @param string $where
    * @return array
    */
    public function getFollowupCountByTids($tidarray, $where = NULL)
    {
        if (count($tidarray) == 0) {
                return array();

        }

        $select = $this->select();
        $select->from($this, array("tid", "count" => new Zend_Db_Expr("count(*)")));
        $select->where('tid IN(?)', $tidarray);
        if ($where) {
                $select->where($where);
        }
        $select->group(array ("tid"));
        //return (string) $select;
        $counts = $this->fetchAll($select)->toArray();
        $result = array();
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
        $select->from($this, array("fid_ref"));
        $select->where('fid=?', $fid);

        $result = $this->fetchAll($select)->toArray();

        return $result;
    }
    public function getRelatedInputsByFid($fid)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($fid)) {
                    throw new Zend_Exception('Given fid must be integer!');
        }
        $select = $this->select();
        $select->from($this, array("tid"));
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
