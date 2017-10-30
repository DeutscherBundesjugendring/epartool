<?php

class Model_FollowupFiles extends Zend_Db_Table_Abstract
{
    const TYPE_GENERAL = 'general';
    const TYPE_SUPPORTING = 'supporting';
    const TYPE_ACTION = 'action';
    const TYPE_REJECTION = 'rejected';
    const TYPE_END = 'end';

    const UPLOAD_SCENARIO_THUMB = 'followup_document_thumb';

    protected $_name = 'fowup_fls';
    protected $_primary = 'ffid';
    protected $_dependentTables = array('Model_Followups');

    /**
     * @return array
     */
    public static function getTypes() {
        $translator = Zend_Registry::get('Zend_Translate');
        return [
            self::TYPE_GENERAL => $translator->translate('General'),
            self::TYPE_SUPPORTING => $translator->translate('Supporting'),
            self::TYPE_ACTION => $translator->translate('Action'),
            self::TYPE_REJECTION => $translator->translate('Rejected'),
            self::TYPE_END => $translator->translate('End'),
        ];
    }

    /**
     * getByKid
     * @desc get reaction_files by consultation id
     * @param  integer $kid
     * @param  string  $order
     * @param  integer $limit
     * @return array
     *
     */
    public function getByKid($kid, $order = null, $limit = null, $excludeFfid = null)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($kid)) {
            return [];
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
     * returns entry by fowup_fls.ffid
     * @param  integer $ffid
     * @return array
     */
    public function getById($ffid, $withoutsnippets = false)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($ffid)) {
            return [];
        }

        $result = [];
        $row = $this->find($ffid)->current();
        if ($row) {
            $result = $row->toArray();
            if (!$withoutsnippets) {
                $depTable = new Model_Followups();
                $depTableSelect = $depTable->select();
                $depTableSelect->order('docorg ASC');
                $result['fowups'] = $row->findDependentRowset($depTable, null, $depTableSelect)->toArray();
            }
        }

        return $result;
    }
    /**
     * returns entry by fowup_fls.ffid
     * @param  array $idArray
     * @return array
     */
    public function getByIdArray(array $idArray)
    {
        if (!$idArray) {
            return [];
        }

        $select = $this->select();
        $select->where('ffid IN(?)', $idArray);

        return $this->fetchAll($select)->toArray();
    }

    /**
     * get fowups by fowups_fls.ffid
     * @param  integer              $ffid
     * @param  string               $order
     * @return Zend_DB_Table_Rowset|array
     */
    public function getFollowupsById($ffid, $order = null)
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($ffid)) {
            return [];
        }

        $depTable = new Model_Followups();
        $depTableSelect = $depTable->select();

        if ($order) {
            $depTableSelect->order($order);
        }

        $row = $this->find($ffid)->current();
        if ($row) {
            return $row->findDependentRowset($depTable, null, $depTableSelect);
        }

        return [];
    }

    /**
     * @param int $ffid
     * @return int
     */
    public function getFollowupsCountById($ffid)
    {
        $followups = new Model_Followups();

        $select = $followups->select()
            ->from(['f' => $followups->info(self::NAME)], [new Zend_Db_Expr('COUNT(*) as count')])
            ->where('f.ffid = ?', (int) $ffid);

        return (int) $followups->fetchAll($select)->current()->count;
    }

    /**
     * Returns reaction_files with the associated reaction_snippets
     * @param  array $wheres An array of where conditions
     * @return array         An array of the reaction_file arrays
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
     * @return int The number of rows deleted.
     * @throws \Dbjr_Exception
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
            throw new Dbjr_Exception('Cant delete reaction & impact if snippets exist.');
        }

        return parent::delete($where);
    }
}
