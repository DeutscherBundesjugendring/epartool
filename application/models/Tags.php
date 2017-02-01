<?php
/**
 * Tags
 * @desc        Class of Tags,
 * @author    Jan Suchandt
 */
class Model_Tags extends Dbjr_Db_Table_Abstract
{

    const FREQUENCY_RARE = 33;
    const FREQUENCY_MEDIUM = 66;

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
     * @param  string   $vot  true for inputs that are confirmed for voting
     * @param  bool     $excludeInvisible
     * @param  bool     $withoutAdmin
     * @return array          An array in form [tagId => [count => $occurenceCount, frequency => $frequency]]
     */
    public function getAllByConsultation($kid, $vot = null, $excludeInvisible = false, $withoutAdmin = false)
    {
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
                ->where('is_confirmed <> ?', false)
                ->where('is_confirmed_by_user = ?', true);
        }
        if ($withoutAdmin) {
            $select->where('(uid IS NOT NULL OR confirmation_key IS NOT NULL)');
        }
        if (!empty($vot)) {
            if ($vot === null) {
                $select->where('i.is_votable IS NULL');
            } else {
                $select->where('i.is_votable = ?', $vot);
            }
        }
        $tags = $this->fetchAll($select);

        return $this->calculateFrequency($tags);
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

    /**
     * @param Zend_Db_Table_Rowset_Abstract $tags
     * @return array
     */
    private function calculateFrequency($tags)
    {
        $frequencies = [];
        $max = 0;
        $min = PHP_INT_MAX;
        foreach ($tags as $tag) {
            $frequencies[$tag['tg_nr']] = $tag->toArray();
            if ((int) $tag['count'] < $min) {
                $min = $tag['count'];
            }
            if ((int) $tag['count'] > $max) {
                $max = $tag['count'];
            }
        }

        $interval = $max - $min;

        foreach ($tags as $tag) {
            if ($interval > 0) {
                $weight = ((int) $tag['count'] - $min) * 100 / $interval;
                if ($weight < self::FREQUENCY_RARE) {
                    $frequencies[$tag['tg_nr']]['frequency'] = 'rare';
                } elseif ($weight >= self::FREQUENCY_RARE && $weight < self::FREQUENCY_MEDIUM) {
                    $frequencies[$tag['tg_nr']]['frequency'] = 'medium';
                } elseif ($weight >= self::FREQUENCY_MEDIUM) {
                    $frequencies[$tag['tg_nr']]['frequency'] = 'frequented';
                }
            } else {
                $frequencies[$tag['tg_nr']]['frequency'] = 'medium';
            }
        }
        return $frequencies;
    }
}
