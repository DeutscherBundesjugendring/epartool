<?php

class Model_GroupSize extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'group_size';
    protected $_primary = 'id';

    /**
     * @param int $consultationId
     * @return array
     */
    public function getByConsultation($consultationId)
    {
        $select = $this->select()->where('consultation_id = ?', $consultationId);
        
        return $this->fetchAll($select);
    }

    /**
     * @return null|\Zend_Db_Table_Row_Abstract
     */
    public function getSingle()
    {
        $select = $this->select()->where('consultation_id IS NULL');

        return $this->fetchRow($select);
    }
    
    /**
     * @param $consultationId
     * @return array
     */
    public function getOptionsByConsultation($consultationId)
    {
        $select = $this->select();
        $select->where('consultation_id = ?', $consultationId)
            ->order(['from ASC', 'to ASC']);

        $data = $this->fetchAll($select);
        $options = [];
        if (count($data) > 0) {
            foreach ($data as $d) {
                $options[$d['id']] = $d['from'] . ($d['to'] === null ? ' +' : ' - ' . $d['to']);
            }
        }
        
        return $options;
    }

    /**
     * @param int $consultationId
     * @return null|\Zend_Db_Table_Row_Abstract
     */
    public function getInitGroupSize($consultationId)
    {
        $select = $this->select();
        $select
            ->where('consultation_id = ?', $consultationId)
            ->where('`from` = ?', 1)
            ->where('`to` = ?', 2);
        
        return $this->fetchRow($select);
    }
}
