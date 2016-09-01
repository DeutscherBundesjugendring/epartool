<?php

class Model_ContributorAge extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'contributor_age';
    protected $_primary = 'id';

    /**
     * @param int $consultationId
     * @return array
     */
    public function getByConsultation($consultationId)
    {
        $select = $this->select();
        $select->where('consultation_id = ?', $consultationId);
        return $this->fetchAll($select);
    }

    /**
     * @param int $consultationId
     * @return array
     */
    public function getOptionsByConsultation($consultationId)
    {
        $select = $this->select();
        $select
            ->where('consultation_id = ?', $consultationId)
            ->order(['from ASC', 'to ASC']);

        $data = $this->fetchAll($select);
        $options = [];
        if (count($data) > 0) {
            $translator = Zend_Registry::get('Zend_Translate');
            foreach ($data as $d) {
                if ($d['consultation_id'] === null && (int) $d['from'] === 1 && $d['to'] === null) {
                    $options[$d['id']] = $translator->translate('all age groups');
                } else {
                    $options[$d['id']] = $d['from'] . ($d['to'] === null ? ' +' : ' - ' . $d['to']);
                }
            }
        }

        return $options;
    }
}
