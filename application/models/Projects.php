<?php
/**
 * Projects
 * @author Markus Hackel
 *
 */
class Model_Projects extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'proj';
    protected $_primary = 'proj';

    /**
     * Returns all entries from the proj table
     *
     * @param  string                        $order [optional]
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getAll($order = '')
    {
        $select = $this->select();
        if (!empty($order)) {
            $select->order($order);
        }

        return $this->fetchAll($select);
    }
}
