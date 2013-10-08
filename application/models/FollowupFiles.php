<?php

/**
 * Description of Model FollowupFiles
 *
 * @author Marco Dinnbier
 */
class Model_FollowupFiles extends Zend_Db_Table_Abstract
{
    protected $_name = 'fowup_fls';
    protected $_primary = 'ffid';

    protected $_dependentTables = array('Model_Followups');

    /**
     * getByKid
     * @desc get followup-files by consultation id
     * @param integer $kid
     * @param string $order
     * @param integer $limit
     * @return array
     *
     */
    public function getByKid($kid, $order = NULL, $limit = NULL, $excludeFfid = NULL)
    {
        //$result = array();

        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($kid)) {
            return array();
        }
        $select = $this->select();
        $select->where('kid=?', $kid);

        if ($order) {
            $select->order($order);
        }
        if ($limit) {

            $select->limit($limit);
        }
        if ($excludeFfid) {
            $select->where('ffid!=?', $excludeFfid);
            
        }
        $result = $this->fetchAll($select);

        return $result->toArray();

    }

    /**
     * getById
     * returns entry by fowup_fls.ffid
     * @param integer $ffid
     * @return array
     */
    public function getById($ffid, $withoutsnippets = false)
    {
        // is int?
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($ffid)) {
            return array();
        }
        $result = array();
        $row = $this->find($ffid)->current();
        if ($row) {
            $result = $row->toArray();
            //$result['when'] = strtotime($result['when']);
            if(!$withoutsnippets) {
                $depTable = new Model_Followups();
                $depTableSelect = $depTable->select();
                $depTableSelect->order('docorg ASC');

                $rowset = $row->findDependentRowset($depTable, NULL, $depTableSelect);

                $result['fowups'] = $rowset->toArray();
                
            }
        }
        return $result;
    }
    /**
     * getById
     * returns entry by fowup_fls.ffid
     * @param integer $ffid
     * @return array
     */
    public function getByIdArray($idarray)
    {
        // is int?
       if (!is_array($idarray) || count($idarray) == 0) {
          
          return array();
          
        }
        
        $select = $this->select();
        $select->where('ffid IN(?)', $idarray);

        return $this->fetchAll($select)->toArray();
        
       
    }

    /**
     * deleteById
     * delete entry by id
     * @param integer $id
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
     * getFollowupsById
     * get fowups by fowups_fls.ffid
     *
     * @param integer $ffid
     * @param string $order
     * @return Zend_DB_Table_Rowset
     */
    public function getFollowupsById($ffid, $order = NULL)
    {
        //echo $id;
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($ffid)) {
            return array();
        }

        $depTable = new Model_Followups();
        $depTableSelect = $depTable->select();

        if ($order) {
            $depTableSelect->order($order);
        }

        $row = $this->find($ffid)->current();
        if ($row) {

            $rowset = $row->findDependentRowset($depTable, NULL, $depTableSelect);
            return $rowset;
        } else {

            return array();

        }


    }

}

?>
