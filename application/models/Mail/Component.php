<?php

class Model_Mail_Component extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'email_component';
    protected $_primary = 'id';



    /**
     * Adds new component to db
     * @param  array   $data The data to be inserted
     * @return integer       Primary key of the inserted entry
     */
    public function insert(array $data)
    {
        $row = $this->createRow($data);
        $row->project_code = $this->_projectCode;

        return parent::insert($row->toArray());
    }

    /**
     * Updates existing component
     * @param  array                $data  Column-value pairs.
     * @param  array|string         $where An SQL WHERE clause, or an array of SQL WHERE clauses.
     * @throws Dbjr_Mail_Exception         Thrown if editing template from another project or changing name in system template
     * @return int                         The number of rows updated.
     */
    public function update(array $data, $where)
    {
        if ($data['project_code'] !== $this->_projectCode) {
            throw new Dbjr_Mail_Exception('Can not update component belonging to another project.');
        }

        return parent::update($data, $where);
    }

    /**
     * Deletes component from database. Only performs delete
     * @param  array|string         $where SQL WHERE clause(s).
     * @return integer                     The number of rows deleted
     * @throws Dbjr_Mail_Exception         Thrown if deleting template from another project or a system template
     */
    public function delete($where)
    {
        $db = $this->getAdapter();

        $db->beginTransaction();
        try {
            $select = $this->select();
            if (is_array($where)) {
                foreach ($where as $key => $val) {
                    $select->where($key, $val);
                }
            } else {
                $select->where($where);
            }
            $component = $this->fetchRow($select);

            if (!isset($component)) {
                throw new Dbjr_Mail_Exception('Can not delete component belonging to another project.');
            }
            $rowsDeleted = parent::delete($where);
            $db->commit();

            return $rowsDeleted;
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    /**
     * Return the select object modified to only include templates from the current project
     * @param  bool                 $withFromPart Whether or not to include the from part of the select based on the table
     * @param  Dbjr_Db_Criteria     $criteria     The criteria object
     * @return Zend_Db_Table_Select               The select object
     */
    public function select($withFromPart = Zend_Db_Table_Abstract::SELECT_WITHOUT_FROM_PART, $criteria = null)
    {
        return parent::select($withFromPart, $criteria)->where('project_code=?', $this->_projectCode);
    }
}
