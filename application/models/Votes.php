<?php

class Model_Votes extends Dbjr_Db_Table_Abstract
{
    protected $_name = 'vt_final';
    protected $_primary = 'id';
    protected $_referenceMap = ['Consultations' => [
        'columns' => 'kid',
        'refTableClass' => 'Model_Consultations',
        'refColumns' => 'kid'
    ]];

    /**
     * Prepare cache options
     */
    protected $_frontendName;
    protected $_backendName;
    protected $_frontendOptions = array();
    protected $_backendOptions = array();

    /**
     * Model_Votes constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/VotingCache.ini', APPLICATION_ENV);
        $this->_frontendName = $config ->cacheVotingResults->frontend->name;
        $this->_backendName = $config->cacheVotingResults->backend->name;
        $this->_frontendOptions = $config ->cacheVotingResults->frontend->options->toArray();
        $this->_backendOptions = $config->cacheVotingResults->backend->options->toArray();
    }


    /**
     * Returns the voting results from database with fallback if no finished results in DB
     * @param int $kid
     * @param int $qid
     * @return array
     * @throws \Zend_Validate_Exception
     */
    public function getResultsValues($kid, $qid = 0)
    {
        $questionArray = $this->getQuestionArray($kid, $qid);
        if (count($questionArray[1])) {
            $currentQuestion = $questionArray['0'];
            $questions = $questionArray['1'];
            $votes = (new Model_VotingFinal)->getFinalVotesByQuestion($currentQuestion['qi']);

            if (!empty($votes)) {
                return [
                    'currentQuestion' => $currentQuestion,
                    'questions' => $questions,
                    'votings' => $votes,
                    'highest_rank' => $votes[0]['rank']
                ];
            }

            return $this->getResultsValuesFromDB($kid, $currentQuestion['qi']);
        }

        return [];
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
        $votings = [];
        $thesesVotes = [];
        $thesesValues = [];
        $rank = [];
        $cast = [];

        // prepare cache
        Zend_Loader::loadClass('Zend_Cache');
        $cacheResultValues = Zend_Cache::factory(
            $this->_frontendName,
            $this->_backendName,
            $this->_frontendOptions,
            $this->_backendOptions
        );
        $cacheName = 'voting_results_kid_' . $kid . '_qid_' . $qid;

        //start caching
        $resultValues = $cacheResultValues->load($cacheName);
        if (!$resultValues) {
            // see getQuestionArray($kid,$qid) get the questions
            $questionArray = $this->getQuestionArray($kid, $qid);
            $currentQuestion = $questionArray['0'];
                $questions = $questionArray['1'];

            // get the inputs/theses for one question
            $inputsModel = new Model_Inputs();
            $theses = $inputsModel->getVotingthesesByQuestion($currentQuestion['qi']);

            // get voting values and build helper arrays
            $votesIndivModel = new Model_Votes_Individual();
            $followUpModel = new Model_FollowupsRef();
            foreach ($theses as $thesis) {
                $thesesVotes[$thesis['tid']] = $votesIndivModel->getVotingValuesByThesis($thesis['tid'], $kid);
                $thesesValues[$thesis['tid']] = $thesis;
                // get reaction_files?
                $followUps = $followUpModel->getFollowupCountByTids($thesis['tid']);
                $thesesValues[$thesis['tid']]['is_followups'] = false;
                if (isset($followUps[$thesis['tid']])) {
                    $theses_values[$thesis['tid']]['is_followups'] = true;
                }
                //build votings array
                $votings[] = array_merge($thesesValues[$thesis['tid']], $thesesVotes[$thesis['tid']]);

                // Arrays for multisort
                $rank[] = $thesesVotes[$thesis['tid']]['rank'];
                $cast[] = $thesesVotes[$thesis['tid']]['cast'];
            }

            // sort votings array
            array_multisort($rank, SORT_DESC, $cast, SORT_ASC, $votings);

            $votingsRank = 0;
            if (isset($votings[0]['rank'])) {
                $votingsRank = $votings[0]['rank'];
            }

            $resultValues = [
                'currentQuestion' => $currentQuestion,
                'questions' => $questions,
                'votings' => $votings,
                'highest_rank' => $votingsRank
            ];
            // save the resultValues in cache
            $cacheResultValues -> save($resultValues, $cacheName);
        }

        return $resultValues;
    }

    /**
     * @param int $kid
     * @param int $qid
     * @return array
     * @throws \Zend_Exception
     */
    protected function getQuestionArray($kid, $qid)
    {
        $questionArray = [];
        $currentQuestion = [];
        $questions = (new Model_Questions())->getByConsultation($kid);

        foreach ($questions as $question) {
            if (!$qid) {
                $currentQuestion = $question;
                 break;
            } elseif ($qid === $question['qi']) {
                $currentQuestion = $question;
                break;
            }
        }
        $questionArray['0'] = $currentQuestion;
        $questionArray['1'] = $questions ;

        return $questionArray;
    }
}
