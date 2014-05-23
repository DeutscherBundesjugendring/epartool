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

        return (int) $this -> insert($data);
    }
    public function getById($id)
    {
            $result = array();
            $row = $this -> find($id) -> current();
            if ($row) {
                $result = $row -> toArray();
            } else {
                $this ->add($id);
                $row = $this -> find($id) -> current();
                $result = $row -> toArray();
            }

        return $result;
    }

}
