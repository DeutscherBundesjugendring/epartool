<?php
/**
 * Votes_Uservotes
 * @author Karsten Tackmann
 */
class Model_Votes_Uservotes  extends Model_DbjrBase
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
                        ->where('inputs.kid = ?', $kid)
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
                                        array('tags.tg_de AS tagname_de',
                                                'tags.tg_en AS tagname_en')
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

}
