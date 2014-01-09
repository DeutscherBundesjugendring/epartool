<?php
/**
 * Base Class for all Models
 *
 * @author Markus Hackel
 */
class Model_DbjrBase extends Zend_Db_Table_Abstract
{
    /**
     * Adds filter for field 'project' if appropriate column is available
     * @see Zend_Db_Table_Abstract::select()
     */
    public function select($withFromPart = parent::SELECT_WITHOUT_FROM_PART)
    {
        // get select Object from parent abstract class
        $select = parent::select($withFromPart);

        $cols = $this->_getCols();
        $project = Zend_Registry::get('systemconfig')->project;

        if (!empty($project) && in_array('proj', $cols) && $this->_name != 'proj') {
            $select->where('proj LIKE ?', "%{$project}%");
        }

        return $select;
    }
}
