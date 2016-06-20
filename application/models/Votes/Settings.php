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

    public function add($consultationID)
    {
        $data = array('kid' => $consultationID);

        return (int) $this->insert($data);
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
