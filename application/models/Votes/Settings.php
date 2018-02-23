<?php
/**
 * Voting Settings
 *
 * @description   Form of consultation
 * @author        Karsten Tackmann
 */
class Model_Votes_Settings extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'vt_settings';
    protected $_primary = 'kid';

    /**
     * @param int $consultationId
     * @return bool
     */
    public function add($consultationId)
    {
        $data = array('kid' => $consultationId);

        $result = (int) $this->insert($data);

        $resultButtonSets = (new Model_VotingButtonSet())->createDefault($consultationId);
        
        return $result && $resultButtonSets;
    }

    /**
     * @param int $id
     * @return array
     * @throws \Zend_Db_Table_Exception
     */
    public function getById($id)
    {
        $row = $this->find($id)->current();
        if ($row) {
            return $row->toArray();
        } else {
            $this->add($id);
            $row = $this->find($id)->current();
            return $row->toArray();
        }
    }

}
