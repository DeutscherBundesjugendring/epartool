<?php
/**
 * Articles
 * @desc        Class of articles
 * @author    Jan Suchandt
 */
class Model_Articles extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'articles';
    protected $_primary = 'art_id';

    protected $_referenceMap = array(
        'Consultations' => array(
            'columns' => 'kid',
            'refTableClass' => 'Model_Consultations',
            'refColumns' => 'kid'
        )
    );

    /**
     * getById
     * @desc returns entry by id
     * @name getById
     * @param  integer $id
     * @return array
     */
    public function getById($id)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return array();
        }
        $result = array();
        $row = $this->find($id)->current();
        if ($row) {
            $result = $row->toArray();
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
        if (!isset($data['proj'])) {
            $data['proj'] = Zend_Registry::get('systemconfig')->project;
        }

        return (int) $this->insert($data);
    }

    /**
     * updateById
     * @desc update entry by id
     * @name updateById
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

        $where = $this->getDefaultAdapter()
                ->quoteInto($this->_primary[1] . '=?', $id);

        return $this->update($data, $where);
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

        $children = $this->getChildren($id)->count();
        if ($children > 0) {
            return 0;
        }

        // where
        $where = $this->getDefaultAdapter()
                ->quoteInto($this->_primary[1] . '=?', $id);
        $result = $this->delete($where);

        return $result;
    }

    /**
     * Get all Articles that are not assigned to any Consultation, i.e. general Articles
     * @param string $orderBy [optional] Fieldname
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getAllWithoutConsultation($orderBy = 'art_id')
    {
        return $this->getByConsultation(null, null, $orderBy);
    }

    /**
     * Get all Articles incl. subpages of a consultation
     *
     * @param  integer $kid     Id of consultation
     * @param  string  $scope   Scope, e.g. 'info', 'reaction_file' etc.
     * @param  string  $orderBy [optional] Fieldname
     * @return array
     */
    public function getByConsultation($kid = null, $scope = null, $orderBy = 'art_id')
    {
        // first all first level pages
        $select = $this->select()
            ->where('parent_id IS NULL')
            ->order($orderBy);

        if ($kid) {
            $select->where('kid = ?', $kid);
        } else {
            $select->where('kid IS NULL');
        }

        $refNameModel = new Model_ArticleRefNames();
        if (isset($scope) && $refNameModel->scopeExists($scope)) {
            $select->where('ref_nm IN (?)', $refNameModel->getRefNamesByScope($scope));
        }

        $articles = $this->fetchAll($select)->toArray();

        // then their subpages
        foreach ($articles as $key => $article) {
            $subpages = $this->getChildren($article['art_id'])->toArray();
            $articles[$key]['subpages'] = array();
            foreach ($subpages as $subpage) {
                $articles[$key]['subpages'][$subpage['art_id']] = $subpage;
            }
        }

        return $articles;
    }

    /**
     * Returns article by given RefName, e.g. 'about', 'imprint' etc.
     *
     * @param  string  $ref
     * @param  int|null $kid
     * @return array
     */
    public function getByRefName($ref, $kid = null)
    {
        $select = $this->select()->where('ref_nm = ?', $ref);
        if ($kid) {
            $select->where('kid = ?', $kid);
        } else {
            $select->where('kid IS NULL');
        }

        $row = $this->fetchAll($select)->current();
        if (!empty($row)) {
            return $row->toArray();
        }

        return [];
    }

    /**
     * Search in articles by consultations
     * @param string $needle
     * @param integer $consultationId
     * @param integer $limit
     * @return array
     */
    public function search($needle, $consultationId = null, $limit = 30)
    {
        $result = [];
        if ($needle !== '' && is_int($limit)) {
            $select = $this->select();
            $select->from(['ar'=>'articles'], ['art_id', 'desc', 'ref_nm']);
            $select->where('LOWER(ar.artcl) LIKE ?', '%'.$needle.'%');
            // if no consultation is set, search in general articles
            if ($consultationId > 0) {
                $select->where('ar.kid = ?', $consultationId);
            } else {
                $select->where('ar.kid IS NULL');
            }

            $select->limit($limit);
            $result = $this->fetchAll($select)->toArray();
        }

        return $result;
    }

    /**
     * @param int $kid
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getFirstLevelEntries($kid)
    {
        $select = $this->select();
        $select
            ->where('kid = ?', $kid)
            ->where('parent_id IS NULL');

        return $this->fetchAll($select);
    }

    /**
     * @param int $id
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getChildren($id)
    {
        $select = $this->select();
        $select->where('parent_id = ?', $id);

        return $this->fetchAll($select);
    }

    /**
     * @return array
     */
    public function getStaticPages()
    {
        return $this->getByConsultation(null, Model_ArticleRefNames::SCOPE_STATIC);
    }

    /**
     * @param int $consultationId
     * @param string $ref_nm
     * @return int
     * @throws \Zend_Db_Table_Exception
     */
    public function getCountByConsultationAndType($consultationId, $ref_nm)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['a' => $this->info(self::NAME)], [new Zend_Db_Expr('COUNT(*) as count')]);
        if ($consultationId === null) {
            $select->where('a.kid IS NULL');
        } else {
            $select->where('a.kid = ?', $consultationId);
        }

        $select
            ->where('a.ref_nm = ?', $ref_nm)
            ->where('a.is_showed = ?', true);

        return (int) $this->fetchAll($select)->current()->count;
    }
}
