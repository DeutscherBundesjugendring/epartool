<?php
/**
 * Questions
 * @desc        Class of questions, every consultation has questions (count n), users can write entries for every question
 * @author    Jan Suchandt
 */
class Model_Questions extends Model_DbjrBase
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
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($id)) {
            return array();
        }

        $row = $this->find($id)->current();
        // ohne Tags
        $subRow = $row->findDependentRowset('Model_Inputs');

        // hole zu jedem Input noch die zugeordneten Tags mit
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
        $select->order(array('nr ASC', 'q ASC'));

        return $this->fetchAll($select);
    }

    /**
     * Returns next question of consultation, specified by field 'nr'
     *
     * @param  integer                   $qid
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
        $select->where('kid=?', $current->kid)->where('nr>?', $current->nr);
        $select->order('nr');
        $select->limit(1);

        return $this->fetchRow($select);
    }

    /**
     * Get max qi
     *
     * @return integer
     */
    public function getMaxId()
    {
        $row = $this->fetchAll(
                        $this->select()
                                ->from($this, array(new Zend_Db_Expr('max(qi) as maxId')))
                        )->current();

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
            $options[$row->qi] = $row->nr . ' ' . $row->q;
        }

        return $options;
    }

    /**
     * Liefert ein Array mit Fragen inkl. Beiträgen, die ein bestimmter Nutzer ($uid)
     * verfasst hat. Es werden nur Fragen zurückgeliefert, für welche Beiträge des
     * Nutzers existieren
     *
     * @param  integer $uid User ID
     * @param  integer $kid Consultation ID
     * @return array
     */
    public function getWithInputsByUser($uid, $kid)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($uid)) {
            throw new Zend_Exception('Given uid must be integer!');
        }
        if (!$validator->isValid($kid)) {
            throw new Zend_Exception('Given kid must be integer!');
        }

        $inputsModel = new Model_Inputs();
        $order = array('when ASC');
        $userInputs = $inputsModel->getByUserAndConsultation($uid, $kid, $order);
        $questions = array();
        foreach ($userInputs as $input) {
            if (!array_key_exists($input['qi'], $questions)) {
                $question = $this->find($input['qi'])->current();
                if (!empty($question)) {
                    $questions[$input['qi']] = $question->toArray();
                }
            }
            if (array_key_exists($input['qi'], $questions)) {
                $questions[$input['qi']]['inputs'][] = $input;
            }
        }
        // Sortieren nach nr
        $aReturn = array();
        foreach ($questions as $question) {
            $aReturn[$question['nr']] = $question;
        }

        return $aReturn;
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
            $select ->where("LOWER(qu.q) LIKE '%$needle%' OR LOWER(qu.q_xpl) LIKE '%$needle%'");
            // if no consultation is set, search in generell articles
            $select->where('qu.kid = ?', $consultationId);
            $select->limit($limit);

            $result = $this->fetchAll($select)->toArray();

        }

        return $result;
    }
}
