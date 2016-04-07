<?php

class Model_VotingFinal extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'vt_final';
    protected $_primary = 'id';

    /**
     * Inserts final Votes
     * @see CloseController|admin: writeResultsAction();
     * @param array $data
     **/
    public function addOrUpdateFinalVote($data)
    {
        $data['id'] = $this -> getPrimaryKey($data);

        // its possible the row exists
        try {
            $result = $this->createRow($data);
            $new =    (int) $result->save();
        } catch (Exception $e) {
            $row = $this->find($data['id'])->current();
            $row->setFromArray($data);
            return $row->save();
        }
    }

    /**
     * Returns all final Votes by a question
     * @param int $qid
     * @param int $uid
     * @return array
     */
    public function getFinalVotesByQuestion($qid, $uid = 0)
    {
        //Overall votings  votes.uid = 0 otherwise its a voting result from a group
        $db = $this->getAdapter();
        $select = $db->select()
            ->from(['inputs' => 'inpt'])
            ->joinRight(['votes' => 'vt_final'], '(inputs.tid = votes.tid)')
            ->where('inputs.qi = ?', $qid)
            ->where('votes.uid = ?', $uid)
            ->order('votes.rank DESC')
            ->order('votes.cast ASC')
            ->group('inputs.tid');
        $resultSet = $db->query($select);

        $inputs = [];
        foreach ($resultSet as $row) {
            $inputs[] = $row;
        }

        return $inputs;
    }

    /**
     * @param int $inputId
     * @return int
     */
    public function getFinalVoteRankByInputId($inputId)
    {
        $db = $this->getAdapter();
        $select = $db->select()
            ->from(['votes' => 'vt_final'])
            ->where('votes.tid = ?', $inputId);
        $result = $db->query($select)->fetch();
        var_dump($result);
        return $result ? $result['rank'] : null;
    }

    /**
     * Updates the final votes with his place
     * @see CloseController|admin: writeResultsFinishAction() ;
     * @param array $data
     * @return array
     */
    public function updateFinalVotePlace($data)
    {
            $data['id'] = $this -> getPrimaryKey($data);
            $row = $this->find($data['id'])->current();
            $row->setFromArray($data);
            return $row->save();
    }

    /**
     * Checks if the Group results written
     * @see  CloseController |admin:indexAction();
     * @param int $kid
     * @return array
     */
    public function isGroupResultWritten($kid)
    {
        $db = $this->getDefaultAdapter();
        $select = $db->select();
        $select
            ->from($this->_name, ['uid', 'kid'])
            ->where('kid = ?', $kid)
            ->group('uid');
        $result = $db->query($select);

        return $result->fetchAll(PDO::FETCH_KEY_PAIR);
    }


    /**
     * Returns the primaryKey in the vo
     * @see  addOrUpdateFinalVote()
     * @param array $data
     * @return string (md5-hash)
     */
    private function getPrimaryKey($data)
    {
        (isset($data['uid'])) ? ($uid = $data['uid']) : ($data['uid'] = 0);
        $data['id'] = md5($data['tid'] . '-' . $data['uid']);

        return $data['id'];
    }
}
