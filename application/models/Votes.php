<?php
/**
 * Votes
 * @desc        Class of votings, final voting result of consultation
 * @author    Jan Suchandt, Markus Hackel
 */
class Model_Votes extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'vt_final';
    protected $_primary = 'id';

    protected $_referenceMap = array(
        'Consultations' => array(
            'columns' => 'kid',
            'refTableClass' => 'Model_Consultations',
            'refColumns' => 'kid'
        ),
    );

    /**
     *    prepare cache options
     * */
    protected $_frontendName;
    protected $_backendName;
    protected $_frontendOptions = array();
    protected $_backendOptions = array();

    public function __construct()
    {
        // load cache options
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/VotingCache.ini', APPLICATION_ENV);
        //set cache options
        $this -> _frontendName = $config ->cacheVotingResults->frontend->name;
        $this -> _backendName = $config->cacheVotingResults->backend->name;
        $this -> _frontendOptions = $config ->cacheVotingResults->frontend->options->toArray();
        $this -> _backendOptions = $config->cacheVotingResults->backend->options->toArray();
    }


    /**
     * getResultsValues
     * @desc get the voting results from database with fallback if no finished results in DB
     * @param  integer                 $kid
     * @param  integer                 $tid
     * @return array
     * @author
     */
    public function getResultsValues($kid, $qid = 0) {

        $questionArray = array();
        $votings = array();
        $resultValues = array();

        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($kid)) throw new Zend_Validate_Exception('Given parameter kid must be integer!');
        if (!$intVal->isValid($qid)) throw new Zend_Validate_Exception('Given parameter qid must be integer!');

        // see getQuestionArray($kid,$qid)
        $questionArray = $this->getQuestionArray($kid,$qid);
        $currentQuestion = $questionArray["0"];
        $questions = $questionArray["1"];

        // get the votings from DB
        $votingFinalModel = new Model_VotingFinal;
        $votings = $votingFinalModel -> getFinalVotesByQuestion($currentQuestion['qi']);

        // Votings finalized with fallback if no finished Vote in DB
        if (!empty($votings)) {
            $resultValues = array('currentQuestion' => $currentQuestion,
                                                'questions' => $questions,
                                                'votings' => $votings,
                                                    'highest_rank' => $votings[0]['rank']);
            return $resultValues;
        } else {
            return $this->getResultsValuesFromDB($kid, $currentQuestion['qi']);
        }
    }

    /**
     * Returns the voting result values for a given consultation and question,
     * returning array can be directly assigned to the view object
     * fallback for getResultsValues()
     * @param  integer $kid
     * @param  integer $qid
     * @return array
     */
    protected function getResultsValuesFromDB($kid, $qid = 0)
    {

        $questionArray = array();
        $votings = array();
        $resultValues = array();
        $theses = array();
        $theses_votes = array();
        $theses_values = array();
        $rank = array();
        $cast = array();

        $intVal = new Zend_Validate_Int();
        if (!$intVal->isValid($kid)) {
            throw new Zend_Validate_Exception('Given parameter kid must be integer!');
        }
        if (!$intVal->isValid($qid)) {
            throw new Zend_Validate_Exception('Given parameter qid must be integer!');
        }

        // prepare cache
        Zend_Loader::loadClass('Zend_Cache');
            $cacheResultValues = Zend_Cache::factory($this -> _frontendName, $this -> _backendName, $this -> _frontendOptions, $this -> _backendOptions);
        $cacheName = 'voting_results_kid_' . $kid . '_qid_' . $qid;

        //start caching
        if (!($resultValues = $cacheResultValues -> load($cacheName))) {

               // see getQuestionArray($kid,$qid) get the questions
                   $questionArray = $this->getQuestionArray($kid,$qid);
                $currentQuestion = $questionArray["0"];
                $questions = $questionArray["1"];

                // get the inputs/theses for one question
                $inputsModel = new Model_Inputs();
                $theses = $inputsModel->getVotingthesesByQuestion($currentQuestion['qi']);

                // get the voting weights
                $votesRightsModel = new Model_Votes_Rights();
                $votingWeights = $votesRightsModel->getWeightsByConsultation($kid);


                // get voting values and build helper arrays
                $votesIndivModel = new Model_Votes_Individual();
                $followUpModel = new Model_FollowupsRef ();
                foreach ($theses as $thesis) {
                    $theses_votes[$thesis['tid']] = $votesIndivModel ->getVotingValuesByThesis($thesis['tid'], $kid, $votingWeights);
                    $theses_values[$thesis['tid']] = $thesis->toArray();
                    // get followUps?
                    $followUps = $followUpModel ->  getFollowupCountByTids($thesis['tid']);
                    $theses_values[$thesis['tid']]['fowups']="n";
                    if (isset($followUps[$thesis['tid']])) $theses_values[$thesis['tid']]['fowups']="y";
                    //build votings array
                    $votings[] = array_merge($theses_values[$thesis['tid']],$theses_votes[$thesis['tid']]);

                    // Arrays for multisort
                    $rank[]    = $theses_votes[$thesis['tid']]['rank'];
                    $cast[] = $theses_votes[$thesis['tid']]['cast'];
                }

                // sort voting sarray
                array_multisort($rank, SORT_DESC, $cast, SORT_ASC, $votings);

                $votingsRank = 0;
                if (isset($votings[0]['rank'])) $votingsRank = $votings[0]['rank'];

                $resultValues = array('currentQuestion' => $currentQuestion,
                                                    'questions' => $questions,
                                                'votings' => $votings,
                                                'highest_rank' => $votingsRank);
                // save the resultValues in cache
                $cacheResultValues -> save($resultValues, $cacheName);
            }

        return $resultValues;
    }


    protected function getQuestionArray($kid,$qid) {

        $questionArray = array();
        $questionModel = new Model_Questions();
        $currentQuestion = array();
        $questions = $questionModel->getByConsultation($kid);
        foreach ($questions as $question) {
            // no question given, so take the first one
            if ($qid == 0) {
                $currentQuestion = $question;
                 break;
            } elseif ($qid == $question['qi']) {
                $currentQuestion = $question;
                break;
             }
        }
        $questionArray["0"] = $currentQuestion;
        $questionArray["1"] = $questions ;
        return $questionArray;
    }
}
