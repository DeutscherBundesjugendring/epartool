<?php

class Model_InputDiscussion extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'input_discussion';
    protected $_primary = array('id');

    /**
     * @param $uid
     * @param $limit
     * @return array
     * @throws \Zend_Db_Table_Exception
     */
    public function getByUserWithDependencies($uid, $limit)
    {
        // fetch
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from(['p' => $this->_name])
            ->join(
                ['i' => (new Model_Inputs())->info(Model_Inputs::NAME)],
                'i.tid = p.input_id',
                ['tid']
            )->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                ['q', 'qi']
            )->join(
                ['cnslt' => (new Model_Consultations())->info(Model_Consultations::NAME)],
                'cnslt.kid = q.kid',
                ['kid', 'titl']
            )
            ->where('cnslt.proj = ?', $this->_projectCode)
            ->where('uid=?', $uid)
            ->order('p.time_created DESC')
            ->limit($limit);
        $result = $this->fetchAll($select);

        return $result->toArray();
    }

    /**
     * @param $uid
     * @throws Zend_Db_Table_Exception
     * @return string
     */
    public function getCountByUser($uid)
    {
        $select = $this->select()
            ->from(['p' => $this->_name], array(new Zend_Db_Expr('COUNT(*) as count')))
            ->join(
                ['i' => (new Model_Inputs())->info(Model_Inputs::NAME)],
                'i.tid = p.input_id',
                []
            )->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = i.qi',
                []
            )->join(
                ['cnslt' => (new Model_Consultations())->info(Model_Consultations::NAME)],
                'cnslt.kid = q.kid',
                []
            )
            ->where('cnslt.proj = ?', $this->_projectCode)
            ->where('uid = ?', $uid);

        $row = $this->fetchAll($select)->current();

        return $row->count;
    }


}
