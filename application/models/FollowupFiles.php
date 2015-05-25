<?php

class Model_FollowupFiles extends Zend_Db_Table_Abstract
{
    const UPLOAD_SCENARIO_THUMB = 'followup_document_thumb';

    protected $_name = 'fowup_fls';
    protected $_primary = 'ffid';

    protected $_dependentTables = array('Model_Followups');

    /**
     * getByKid
     * @desc get follow-up files by consultation id
     * @param  integer $kid
     * @param  string  $order
     * @param  integer $limit
     * @return array
     *
     */
    public function getByKid($kid, $order = NULL, $limit = NULL, $excludeFfid = NULL)
    {
        //$result = array();

        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($kid)) {
            return array();
        }
        $select = $this->select();
        $select->where('kid=?', $kid);

        if ($order) {
            $select->order($order);
        }
        if ($limit) {

            $select->limit($limit);
        }
        if ($excludeFfid) {
            $select->where('ffid!=?', $excludeFfid);

        }
        $result = $this->fetchAll($select);

        return $result->toArray();

    }

    /**
     * getById
     * returns entry by fowup_fls.ffid
     * @param  integer $ffid
     * @return array
     */
    public function getById($ffid, $withoutsnippets = false)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($ffid)) {
            return array();
        }
        $result = array();
        $row = $this->find($ffid)->current();
        if ($row) {
            $result = $row->toArray();
            //$result['when'] = strtotime($result['when']);
            if (!$withoutsnippets) {
                $depTable = new Model_Followups();
                $depTableSelect = $depTable->select();
                $depTableSelect->order('docorg ASC');

                $rowset = $row->findDependentRowset($depTable, NULL, $depTableSelect);

                $result['fowups'] = $rowset->toArray();

            }
        }

        return $result;
    }
    /**
     * getById
     * returns entry by fowup_fls.ffid
     * @param  integer $ffid
     * @return array
     */
    public function getByIdArray($idarray)
    {
        // is int?
        if (!is_array($idarray) || count($idarray) == 0) {
          return array();
        }

        $select = $this->select();
        $select->where('ffid IN(?)', $idarray);

        return $this->fetchAll($select)->toArray();

    }

    /**
     * getFollowupsById
     * get fowups by fowups_fls.ffid
     *
     * @param  integer              $ffid
     * @param  string               $order
     * @return Zend_DB_Table_Rowset
     */
    public function getFollowupsById($ffid, $order = NULL)
    {
        //echo $id;
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($ffid)) {
            return array();
        }

        $depTable = new Model_Followups();
        $depTableSelect = $depTable->select();

        if ($order) {
            $depTableSelect->order($order);
        }

        $row = $this->find($ffid)->current();
        if ($row) {

            $rowset = $row->findDependentRowset($depTable, NULL, $depTableSelect);

            return $rowset;
        } else {
            return array();
        }
    }

    /**
     * Returns follow-ups with the associated snippets
     * @param  array $wheres An array of where conditions
     * @return array         An array of the follow-up arrays
     */
    public function getWithSnippets($wheres)
    {
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from($this->info(self::NAME))
            ->order('docorg')
            ->joinLeft(
                (new Model_Followups())->info(Model_Followups::NAME),
                (new Model_Followups())->info(Model_Followups::NAME) . '.ffid = ' . $this->info(self::NAME) . '.ffid',
                ['fid', 'expl']
            );

        foreach ($wheres as $cond => $value) {
            $select->where($cond, $value);
        }

        $res = $this->fetchAll($select);

        $followups = [];
        foreach ($res as $followup) {
            if (!isset($followups[$followup->ffid])) {
                $followups[$followup->ffid]['titl'] = $followup->titl;
                $followups[$followup->ffid]['snippets'] = [];
            }
            if ($followup->fid) {
                $followups[$followup->ffid]['snippets'][] = $followup;
            }
        }

        return $followups;
    }

    /**
     * Deletes existing rows.
     * @param  array|string $where SQL WHERE clause(s).
     * @return int          The number of rows deleted.
     */
    public function delete($where)
    {
        $followups = $this->fetchAll($where);
        $followupIds = [];
        foreach ($followups as $followup) {
            $followupIds[] = $followup->ffid;
        }

        $snippetModel = new Model_Followups();
        $relatedSnippetCount = $snippetModel
            ->select()
            ->from($snippetModel, ['count' => 'COUNT(*)'])
            ->where('ffid IN (?)', $followupIds)
            ->query()
            ->fetchObject()
            ->count;

        if ($relatedSnippetCount) {
            throw new Dbjr_Exception('Cant delete follow-up if snippets exist.');
        }

        return parent::delete($where);
    }
}
