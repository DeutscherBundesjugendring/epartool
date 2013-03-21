<?php
/**
 * Votes
 * @desc    Class of votings, final voting result of consultation
 * @author  Jan Suchandt, Markus Hackel
 */
class Model_Votes extends Zend_Db_Table_Abstract {
  protected $_name = 'vt_final';
  protected $_primary = array(
    'uid', 'tid'
  );

  protected $_referenceMap = array(
    'Consultations' => array(
      'columns' => 'kid',
      'refTableClass' => 'Model_Consultations',
      'refColumns' => 'kid'
    ),
  );
  
  /**
   * Returns the voting result values for a given consultation and question,
   * returning array can be directly assigned to the view object
   *
   * @param integer $kid
   * @param integer $qid
   * @return array
   */
  public function getResultsValues($kid, $qid = 0) {
    $intVal = new Zend_Validate_Int();
    if (!$intVal->isValid($kid)) {
      throw new Zend_Validate_Exception('Given parameter kid must be integer!');
    }
    if (!$intVal->isValid($qid)) {
      throw new Zend_Validate_Exception('Given parameter qid must be integer!');
    }
    $theses_votes = array();
    $theses_votes_order = array();
    $theses_values = array();
    $questionModel = new Model_Questions();
    $currentQuestion = array();
    $questions = $questionModel->getByConsultation($kid);
    foreach ($questions as $question) {
      if ($qid == 0) {
        // no question given, so take the first one
        $currentQuestion = $question;
        break;
      } elseif ($qid == $question['qi']) {
        $currentQuestion = $question;
        break;
      }
    }
    
    // get the voting theses
    $inputsModel = new Model_Inputs();
    $theses = $inputsModel->getVotingthesesByQuestion($currentQuestion['qi']);
    
    // get voting values and build helper arrays
    $votesIndivModel = new Model_Votes_Individual();
    foreach ($theses as $thesis) {
      $theses_votes_order[$thesis['tid']] = $votesIndivModel
      ->getVotingValuesByThesis($thesis['tid'], $kid);
      $theses_values[$thesis['tid']] = $thesis->toArray();
    }
    
    // build the $theses_votes array
    foreach ($theses_votes_order as $thesisId => $votingValues) {
      $theses_votes[$votingValues['rank']][$thesisId] = $theses_values[$thesisId];
      $theses_votes[$votingValues['rank']][$thesisId]['points'] = $votingValues['points'];
      $theses_votes[$votingValues['rank']][$thesisId]['cast'] = $votingValues['cast'];
    }
    
    // sort the $theses_votes array descending by key (i.e. rank)
    krsort($theses_votes);
    
    // reset pointer to get the highest key (see below)
    reset($theses_votes);
    
    return array(
        'currentQuestion' => $currentQuestion,
        'questions' => $questions,
        'theses_votes' => $theses_votes,
        'highest_rank' => key($theses_votes)
    );
  }
}

