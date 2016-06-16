<?php
/**
 * Votes_Uservotes
 * @author Karsten Tackmann
 */
class Model_Votes_Uservotes  extends Dbjr_Db_Table_Abstract
{
    public function fetchAllInputsWithUserVotes($qid = null,$subUid, $kid,$tagId=null)
    {
                        $intVal = new Zend_Validate_Int();

                        $db = $this->getAdapter();
                        $select = $db->select();
                        $select->from(
                             array('inputs' => 'inpt')
                        )
                        ->joinLeft (
                                array('votes' => 'vt_indiv'),
                                        '(inputs.tid = votes.tid  AND votes.sub_uid="'.$subUid.'")',
                                        array('votes.pts AS points',
                                        		'votes.status AS status',  // add this line for dedugging votes counter
                                                'votes.pimp AS pimp')
                            )
                        ->join(
                            ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                            'q.qi = inputs.qi',
                            null
                        )
                        ->where('kid=?', $kid)
                        ->where('inputs.vot = ?', "y")
                        ->order('inputs.vot DESC')
                        ->group('inputs.tid');

                        if (!is_null($qid)) {

                            if (!$intVal->isValid($qid)) throw new Zend_Validate_Exception('Given parameter tid must be integer!');

                            $select ->where('inputs.qi = ?', $qid);

                        } elseif (!is_null($tagId)) {

                            if (!$intVal->isValid($tagId)) throw new Zend_Validate_Exception('Given parameter tagID must be integer!');

                            $select
                            ->joinLeft(
                                array('tagid' => 'inpt_tgs'),
                                        'tagid.tid = inputs.tid')
                            ->joinLeft (
                                array('tags' => 'tgs'),
                                        '(tags.tg_nr= tagid.tg_nr)',
                                        array('tagname_de' => 'tags.tg_de')
                            )
                            ->where('tagid.tg_nr = ?', $tagId);
                      }

                    $resultSet = $db->query($select);

                    $inputs = array();
                    foreach ($resultSet as $row) {
                        $inputs[]=$row;
                    }

                    return $inputs;
    }

    /**
     * @param string $confirmationHash
     * @return array
     * @throws \Zend_Db_Table_Exception
     */
    public function fetchInputsToConfirm($confirmationHash)
    {
        $db = $this->getAdapter();
        $select = $db->select();
        $select->from(['inputs' => (new Model_Inputs())->info(Model_Inputs::NAME)])
            ->join(
                ['votes' => 'vt_indiv'],
                $db->quoteInto('(inputs.tid = votes.tid  AND votes.confirmation_hash = ?)', $confirmationHash),
                ['points' => 'votes.pts', 'status' => 'votes.status', 'pimp' => 'votes.pimp']
            )->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = inputs.qi',
                []
            )
            ->group('inputs.tid');

        $resultSet = $db->query($select);

        $inputs = array();
        foreach ($resultSet as $row) {
            $inputs[]=$row;
        }

        return $inputs;
    }

}
