<?php
/**
 * Questions
 * @desc        Class of questions, every consultation has questions (count n), users can write entries for every question
 * @author    Jan Suchandt
 */
class Model_Questions extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'quests';
    protected $_primary = 'qi';

    protected $_referenceMap = array(
        'Consultations' => array(
            'columns' => 'kid',
            'refTableClass' => 'Model_Consultations',
            'refColumns' => 'kid'
        ),
        'Inputs' => array(
            'columns' => 'qi',
            'refTableClass' => 'Model_Inputs',
            'refColumns' => 'qi'
        ),
    );

    /**
     * getById
     * @desc returns entry by id
     * @name getById
     * @param  integer $id
     * @param  integer $tag [optional] Filter Beiträge nach Tag
     * @return array
     */
    public function getById($id, $tag = 0)
    {

        $row = $this->find($id)->current();
        $subRow = $row->findDependentRowset('Model_Inputs');

        $modelInputs = new Model_Inputs();
        $inputs = array();
        $tmpInputs = $subRow->toArray();
        foreach ($tmpInputs as $tmpInput) {
            $input = $modelInputs->getById($tmpInput['tid'], $tag);
            if (!empty($input)) {
                $inputs[] = $input;
            }
        }

        $aQuestion = $row->toArray();
        $aQuestion['inputs'] = $inputs;

        return $aQuestion;
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

        // where
        $where = $this->getDefaultAdapter()
                ->quoteInto($this->_primary[1] . '=?', $id);
        $result = $this->delete($where);

        return $result;
    }

    /**
     * getByConsultation
     * @desc returns entries by consultations-id
     * @name getByConsultation
     * @param  integer              $kid id of consultation
     * @return Zend_Db_Table_Rowset
     */
    public function getByConsultation($kid)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($kid)) {
            throw new Zend_Exception('Given kid must be integer!');
        }

        // fetch
        $select = $this->select();
        $select->where('kid=?', $kid);
        $select->order(['nr ASC', 'q ASC']);

        return $this->fetchAll($select);
    }

    /**
     * @param int $consultationId
     * @return int
     */
    public function getCountByConsultation($consultationId)
    {
        $select = $this->select();
        $select->from(['q' => $this->_name], ['count'=>'COUNT(qi)'])->where('kid=?', $consultationId);

        return (int) $this->fetchRow($select)->count;
    }


    /**
     * Returns next question of consultation, specified by field 'nr'
     *
     * @param  int $qid
     * @throws Zend_Exception
     * @return Zend_Db_Table_Rowset|null
     */
    public function getNext($qid)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($qid)) {
            throw new Zend_Exception('Given qid must be integer!');
        }

        $current = $this->find($qid)->current();

        // fetch
        $select = $this->select();
        $select
            ->where('kid = ?', $current['kid'])
            ->where('q > ?', $current['q']);
        if ($current['nr'] === null) {
            $select->where('nr IS NULL');
        } else {
            $select->where('nr = ?', $current['nr']);
        }
        $select->order('q ASC');
        $select->limit(1);
        $row = $this->fetchRow($select);

        if ($row === null) {
            $select = $this->select();
            $select->where('kid = ?', $current['kid']);
            if ($current['nr'] === null) {
                $select->where('nr IS NOT NULL');
            } else {
                $select->where('nr > ?', $current['nr']);
            }
            $select->order(['nr ASC', 'q ASC']);
            $select->limit(1);

            return $this->fetchRow($select);
        }

        return $row;
    }

    /**
     * Get max qi
     *
     * @return integer
     */
    public function getMaxId()
    {
        $row = $this->fetchAll(
            $this
                ->select()
                ->from($this, [new Zend_Db_Expr('max(qi) as maxId')])
        )
        ->current();

        return $row->maxId;
    }

    /**
     * Returns array of multioptions for use in Zend_Form_Element_Select
     * i.e. array of Questions suitable for the given Consultation ID
     *
     * @param  integer $kid Consultation ID
     * @return array
     */
    public function getAdminInputFormSelectOptions($kid = 0)
    {
        $options = array();
        $select = $this->select();
        if ($kid > 0) {
            $select->where('kid = ?', $kid);
        }
        $rowset = $this->fetchAll($select);
        foreach ($rowset as $row) {
            $options[$row['qi']] = (isset($row['nr']) ? $row['nr'] : '') . ' ' . $row['q'];
        }

        return $options;
    }

    /**
     * Search in questions by consultations
     * @param string  $needle
     * @param integer $consultationId
     * @param integer $limit
     */
    public function search($needle, $consultationId, $limit=30)
    {
        $result = array();
        if ($needle !== '' && !empty($consultationId) && is_int($limit)) {
            $select = $this->select();
            $select->from(
                array('qu'=>'quests'),
                array('q_xpl'=>'SUBSTRING(q_xpl,1,100)', 'q', 'qi')
            );
            $select ->where('LOWER(qu.q) LIKE ? OR LOWER(qu.q_xpl) LIKE ?', '%' . $needle . '%');
            // if no consultation is set, search in generell articles
            $select->where('qu.kid = ?', $consultationId);
            $select->limit($limit);

            $result = $this->fetchAll($select)->toArray();

        }

        return $result;
    }

    /**
     * Returns reaction_snippets grouped by question
     * @param  integer $kid      The consultation identifier
     * @param  array   $wheres   An array of [condition => value] arrays to be used in Zend_Db_Select::where()
     * @return array             An array of arrays
     */
    public function getWithInputs($wheres)
    {
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from($this->info(self::NAME))
            ->joinLeft(
                (new Model_Inputs())->info(Model_Inputs::NAME),
                $this->info(self::NAME) . '.qi = ' . (new Model_Inputs())->info(Model_Inputs::NAME) . '.qi'
            )
            ->order(['nr','tid']);

        foreach ($wheres as $cond => $value) {
            $select->where($cond, $value);
        }

        $res = $this->fetchAll($select);

        $inputs = [];
        foreach ($res as $input) {
            if (!isset($inputs[$input['qi']])) {
                $inputs[$input['qi']] = [
                    'q' => $input['q'],
                    'nr' => $input['nr'],
                    'inputs' => [],
                ];
            }
            if ($input->tid) {
                $inputs[$input['qi']]['inputs'][] = $input;
            }
        }

        return $inputs;
    }
}
