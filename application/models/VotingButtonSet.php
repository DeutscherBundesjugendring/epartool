<?php

class Model_VotingButtonSet extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'voting_button_set';
    protected $_primary = ['id'];

    /**
     * @param int $consultationId
     * @param string|null $button_type
     * @return array
     */
    public function getSet(int $consultationId, string $button_type = null)
    {
        $select = $this->select();
        $select->where('consultation_id = ?', $consultationId);
        if ($button_type !== null) {
            $select->where('button_type = ?', $button_type);

            $result = $this->fetchAll($select)->toArray();
            $prepared = [];

            foreach ($result as $row) {
                $prepared[$row['points']] = $row;
            }

            return $prepared;
        }

        $result = $this->fetchAll($select)->toArray();
        $prepared = [];
        foreach ($result as $row) {
            if (!isset($prepared[$row['button_type']])) {
                $prepared[$row['button_type']] = [];
            }

            $prepared[$row['button_type']][$row['points']] = $row;
        }

        return $prepared;
    }

    /**
     * @param int $consultationId
     * @param string|null $set
     * @return int
     */
    public function removeSet(int $consultationId, string $set = null) {
        $where = ['consultation_id = ?' => $consultationId];
        if ($set !== null) {
            $where['set = ?'] = $set;
        }

        return $this->delete($where);
    }
}
