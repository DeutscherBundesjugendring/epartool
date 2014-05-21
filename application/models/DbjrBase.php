<?php
/**
 * Base Class for all Models
 *
 * @author Markus Hackel
 */
class Model_DbjrBase extends Zend_Db_Table_Abstract
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
     * Adds filter for field 'project' if appropriate column is available
     * @see Zend_Db_Table_Abstract::select()
     */
    public function select($withFromPart = Zend_Db_Table_Abstract::SELECT_WITHOUT_FROM_PART)
    {
        // get select Object from parent abstract class
        $select = parent::select($withFromPart);

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
        return $this->_name;
    }
}
