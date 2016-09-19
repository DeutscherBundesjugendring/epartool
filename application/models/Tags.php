<?php
/**
 * Tags
 * @desc        Class of Tags,
 * @author    Jan Suchandt
 */
class Model_Tags extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'tgs';
    protected $_primary = 'tg_nr';

    protected $_dependentTables = array('Model_InputsTags');

    protected $_referenceMap = array(
        'Questions' => array(
            'columns' => 'qi', 'refTableClass' => 'Model_Questions', 'refColumns' => 'qi'
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

        return $this->find($id)->current()->toArray();
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
        $row = $this->createRow();
        $row->setFromArray($data);

        return $row->save();
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

        // where
        $where = $this->getDefaultAdapter()
                ->quoteInto($this->_primary[1] . '=?', $id);
        $result = $this->delete($where);

        return $result;
    }

    /**
     * getByUser
     * @desc returns entry by user-id
     * @name getByUser
     * @param  integer $uid id of user
     * @return array
     */
    public function getByUser($uid)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($uid)) {
            return 0;
        }

        // fetch
        $select = $this->select();
        $select->where('uid=?', $uid);
        $select->order('tg_de');
        $result = $this->fetchAll($select);

        return $result->toArray();
    }

    /**
     * Returns array of options for use in Zend_Form_Element_MultiCheckbox
     * i.e. array of all available Tags
     *
     * @return array
     */
    public function getAdminInputFormMulticheckboxOptions()
    {
        $options = array();
        $select = $this->select();
        $select->order('tg_de');
        $rowset = $this->fetchAll($select);
        foreach ($rowset as $row) {
            $options[$row->tg_nr] = $row->tg_de;
        }

        return $options;
    }

    /**
     * Returns usage count of all tags tied to inputs belonging to this consultation
     * @param  integer  $kid  The consultationt identifier
     * @param  string   $vot  'y' for inputs that are confirmed for voting
     * @param  bool     $excludeInvisible
     * @param  bool     $withoutAdmin
     * @return array          An array in form [tagId => [count => $occurenceCount, frequency => $frequency]]
     */
    public function getAllByConsultation($kid, $vot = '', $excludeInvisible = false, $withoutAdmin = false)
    {
        $inputCount = (new Model_Inputs())->getCountByConsultation($kid);
        $select = $this
            ->select()
            ->from(
                ['t' => $this->info(self::NAME)],
                [new Zend_Db_Expr('it.tg_nr, t.tg_de, COUNT(it.tg_nr) AS count')]
            )
            ->join(
                ['it' => (new Model_InputsTags())->info(Model_InputsTags::NAME)],
                't.tg_nr = it.tg_nr',
                []
            )
            ->join(
                ['i' => (new Model_Inputs())->info(Model_Inputs::NAME)],
                'it.tid = i.tid',
                []
            )
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )
            ->where('q.kid = ?', $kid)
            ->group('it.tg_nr');

        if ($excludeInvisible) {
            $select
                ->where('block<>?', 'y')
                ->where('user_conf=?', 'c');
        }
        if ($withoutAdmin) {
            $select->where('(uid IS NOT NULL OR confirmation_key IS NOT NULL)');
        }
        if (!empty($vot)) {
            $select->where('i.vot = ?', $vot);
        }
        $tags = $this->fetchAll($select);

        $freqs = [];
        foreach ($tags as $tag) {
            $freqs[$tag->tg_nr] = $tag->toArray();
            if ($inputCount > 0) {
                $weight = 100 * $tag['count'] / $inputCount;
                if ($weight < 33) {
                    $freqs[$tag->tg_nr]['frequency'] = 'rare';
                } elseif ($weight >= 33 && $weight < 66) {
                    $freqs[$tag->tg_nr]['frequency'] = 'medium';
                } elseif ($weight >= 66) {
                    $freqs[$tag->tg_nr]['frequency'] = 'frequented';
                }
            } else {
                $freqs[$tag->tg_nr]['frequency'] = 'rare';
            }
        }

        return $freqs;
    }

    /**
     * Returns all rows ordered by tg_de
     * @return Zend_Db_Table_Rowset
     */
    public function getAll()
    {
        return $this->fetchAll($this->select()->order('tg_de'));
    }

    /**
     * returns name by id
     * @param  integer $id
     * @return string
     */
    public function getNameById($id)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return array();
        }

        $row = $this->find($id)->current();
        if ($row) {
            return $row->tg_de;
        } else {
            return '';
        }

    }
}
