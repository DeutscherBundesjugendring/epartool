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
     * @return Zend_Db_Table_Select
     */
    public function select($withFromPart = null)
    {
        $select = new Zend_Db_Table_Select($this);

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
