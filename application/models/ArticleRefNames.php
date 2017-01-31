<?php
/**
 * Model ArticleRefNames
 *
 */
class Model_ArticleRefNames extends Dbjr_Db_Table_Abstract
{
    const ARTICLE_EXPLANATION = 'article_explanation';

    const TYPE_GLOBAL = 'global';
    const TYPE_CONSULTATION = 'consultation';

    const SCOPE_NONE = 'none';
    const SCOPE_INFO = 'info';
    const SCOPE_VOTING = 'voting';
    const SCOPE_FOLLOWUP = 'followup';
    const SCOPE_STATIC = 'static';

    protected $_name = 'articles_refnm';
    protected $_primary = array('ref_nm', 'lng');

    protected $_referenceMap = array(
        'Articles' => array(
            'columns' => 'ref_nm',
            'refTableClass' => 'Model_Articles',
            'refColumns' => 'ref_nm'
        )
    );

    /**
     * Returns all rows ordered by field 'ref_nm'
     * @return Zend_Db_Table_Rowset
     */
    public function getAll()
    {
        $select = $this->select();
        $select->order('ref_nm');

        return $this->fetchAll($select);
    }

    /**
     * Returns all Refnames by type
     *
     * @param  string                             $type
     * @param  string                             $lang
     * @return NULL|Zend_Db_Table_Rowset_Abstract
     */
    public function getAllByType($type = null, $lang = 'de')
    {
        if (is_null($type)) {
            return null;
        }

        $select = $this->select();
        $select->where('type = ?', $type)->where('lng = ?', $lang)->order('ref_nm');

        return $this->fetchAll($select);
    }

    /**
     * Returns array of ref_names for the given scope
     *
     * @param  string $scope
     * @return array
     */
    public function getRefNamesByScope($scope)
    {
        if (is_null($scope)) {
            return [];
        }

        $select = $this->select()
            ->from($this->_name, ['ref_nm'])
            ->where('scope = ?', $scope);

        return $this->fetchAll($select)->toArray();
    }

    /**
     * Checks if records with given scope exist in table
     *
     * @param  string  $scope
     * @return boolean
     */
    public function scopeExists($scope = null)
    {
        if (is_null($scope)) {
            return false;
        }
        $select = $this->select()->where('scope = ?', $scope);
        if (count($this->fetchAll($select)) > 0) {
            return true;
        }

        return false;
    }
}
