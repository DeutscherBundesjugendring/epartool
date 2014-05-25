<?php

class Dbjr_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    /**
     * Holds the project code for the current project
     * @var string
     */
    protected $_projectCode;

    public function __construct()
    {
        parent::__construct();
        $this->_projectCode = Zend_Registry::get('systemconfig')->project;
    }

    /**
     * Returns an instance of a Dbjr_Db_Table_Select object.
     * @param  bool                  $withFromPart Whether or not to include the from part of the select based on the table
     * @param  Dbjr_Db_Criteria      $criteria The criteria object
     * @return Zend_Db_Table_Select
     */
    public function select($withFromPart = null, $criteria = null)
    {
        if ($withFromPart === null) {
            $withFromPart = Zend_Db_Table_Abstract::SELECT_WITHOUT_FROM_PART;
        }

        $select = new Zend_Db_Table_Select($this);
        if (isset($criteria->where)) {
            foreach ($criteria->where as $colCond => $val) {
                $select->where($colCond, $val);
            }
        }
        if (isset($criteria->order)) {
            $select->order($criteria->order);
        }
        if (isset($criteria->columns)) {
            $select->from($this->info(self::NAME), $criteria->columns, $this->info(Zend_Db_Table_Abstract::SCHEMA));
            $withFromPart = Zend_Db_Table_Abstract::SELECT_WITHOUT_FROM_PART;
        }
        if ($withFromPart == Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART) {
            $select->from(
                $this->info(self::NAME),
                Zend_Db_Table_Select::SQL_WILDCARD,
                $this->info(Zend_Db_Table_Abstract::SCHEMA)
            );
        }

        $cols = $this->_getCols();
        if (in_array('proj', $cols) && $this->_name != 'proj') {
            $select->where('proj LIKE ?', '%' . $this->_projectCode . '%');
        }

        return $select;
    }

    /**
     * Getter for table name
     * @return string The name pf the table
     */
    public function getName()
    {
        return $this->info(Zend_Db_Table_Abstract::NAME);
    }
}
