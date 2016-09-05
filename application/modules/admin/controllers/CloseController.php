<?php

class Admin_CloseController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_consultation = null;
    protected $_question = null;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->initView();

        $params = $this->_request->getParams();
        $this->_consultation = $this->_helper->consultationGetter($params);
        if (isset($params['qid'])) {
            $this->_question = $this->getQid($params);
        }
    }

    public function indexAction()
    {
        $this->view->consultation = $this->_consultation;
        $votingRightsModel = new Model_Votes_Rights();

        $votingRights = $votingRightsModel->getByConsultation($this->_consultation["kid"]);

        //group resultes stored in DB vt_final?
        $votingFinalModel = new Model_VotingFinal();
        $resultswritten = $votingFinalModel->isGroupResultWritten($this->_consultation["kid"]);

        foreach ($votingRights as $key=>$value) {
            array_key_exists($value['uid'], $resultswritten)
                ? $votingRights[$key]['vt_finalized'] = "y"
                : $votingRights[$key]['vt_finalized']   = "n";
        }

        $this->view->votingRights = $votingRights;
    }

     public function anonymizeVotesAction()
     {
        $records = 0;
        $inputs = array();

        // gets the input ids
        $inputs = (new Model_Inputs())->getVotingchain($this->_consultation["kid"])['tid'];

        $votesIndivModel = new Model_Votes_Individual();
        foreach ($inputs as $value) {
            $result = 0;
            $result = $votesIndivModel->anonymizeVotes($value);
            $records = $records+$result;
        }

        (new Model_Consultations())->updateById($this->_consultation["kid"], ['vt_anonymized' => 'y']);

        $message = sprintf(
            $this->view->translate('%s sets of data have been deleted. The vote is now anonymised!'),
            $records
        );

        $this->_flashMessenger->addMessage($message, 'success');
        $this->redirect('/admin/close/index/kid/' . $this->_consultation["kid"]);
     }

     public function writeResultsAction()
     {
        $data = array();
        $steps = 50;
        $page = $this->_request->getParam('page', 0);
        $pages = $this->_request->getParam('pages', 0);
        $inputs = (new Model_Inputs())->getVotingchain($this->_consultation["kid"])['tid'];

        $this->_flashMessenger->addMessage("Please wait: saving data.", 'success');

        // vars for redirecting
        $sumInputs = count($inputs);
        $pages = round(($sumInputs / $steps), 0);

        //extract 20 records
        $inputs = array_slice($inputs, $page * $steps, $steps);
        $page = $page + 1;

        //initialise required models
        $votesIndivModel = new Model_Votes_Individual();
        $followUpModel = new Model_FollowupsRef();
        $votingFinal = new Model_VotingFinal();

        // get the voting weights
        $votingWeights = (new Model_Votes_Rights())->getWeightsByConsultation($this->_consultation["kid"]);

        //get and prepare the data for each input, write in vt_final
        foreach ($inputs as $value) {
            $data = $votesIndivModel->getVotingValuesByThesis($value, $this->_consultation["kid"], $votingWeights);
            $data['kid'] =$this->_consultation["kid"];
            $data['tid'] = $value;
            $data['fowups'] = 'n';
            $followUps = $followUpModel ->  getFollowupCountByTids($value);
            if (isset($followUps[$value])) {
                $data['fowups'] = 'y';
            }
            $votingFinal->addOrUpdateFinalVote($data);
        }

        // as often as needed. When finish go to the writeResultsFinishAction()
        if ($page <= $pages) {
            $this->redirect(
                '/admin/close/write-results/kid/' . $this->_consultation["kid"] . '/page/' . $page . '/pages/' . $pages
            );
            $this->view->consultation = $this->_consultation;
        } else {
            $this->redirect(
                '/admin/close/write-results-finish/kid/' . $this->_consultation["kid"] . '/suminputs/' . $sumInputs
            );
        }
    }

    /**
     * Finish the vt_final with the places, updates the consultation table  and says i am ready
     */
    public function writeResultsFinishAction()
    {
        $sumInputs = $this->_request->getParam('suminputs', 0);
        $questions = (new Model_Questions())->getByConsultation($this->_consultation["kid"]);

        // get the sorted vt_final data for each question (rank ASC cast DESC)
        $votingFinal = new Model_VotingFinal();
        $finalVotes = array();
        foreach ($questions as $question) {
            $finalVotes[$question['qi']] = $votingFinal -> getFinalVotesByQuestion($question['qi']);
        }

        $inputsModel = new Model_Inputs();
        // update the place column in vt_final
        // update the place and votes column in inpt
        foreach ($finalVotes as $key=>$value) {
            $data = array();
            foreach ($value as $k => $v) {
                $data['place'] = $k + 1;
                $data['tid'] = $v['tid'];
                $dataInputs = $data;
                // casts = summary votes its in use?? if not delete this and update only the place in inpt table
                $dataInputs['votes'] = $v['cast'];
                $votingFinal->updateFinalVotePlace($data);
                $inputsModel->updateById($v['tid'], $dataInputs);

            }
        }

        (new Model_Consultations())->updateById($this->_consultation["kid"], ['vt_finalized' => 'y']);
        $this->_flashMessenger->addMessage(
            sprintf($this->view->translate('%s sets of data have been created/updated.'), $sumInputs),
            'success'
        );
        $this->redirect('/admin/close/index/kid/' . $this->_consultation["kid"]);
    }

    /**
     * Prepare votingresults from vt_indiv an write in vt_final for groups
     */
     public function writeGroupResultsAction()
     {
        $groupUid = $this->_request->getParam('uid', 0);
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($groupUid)) {
            $this->_flashMessenger->addMessage('No group ID available', 'error');
            $this->redirect('/admin');
        }

        $this->_flashMessenger->addMessage("Please wait: saving data.", 'success');

        $inputs = (new Model_Inputs())->getVotingchain($this->_consultation["kid"]);

        $votesIndivModel = new Model_Votes_Individual();
        $votingFinal = new Model_VotingFinal();
        foreach ($inputs['tid'] as $tid) {
            $data = $votesIndivModel->getVotingValuesByGroupAndThesis($tid, $this->_consultation["kid"], $groupUid);
            $votingFinal->addOrUpdateFinalVote($data);
        }

        $this->redirect(
            '/admin/close/write-group-results-finish/kid/' . $this->_consultation["kid"] . '/uid/' . $groupUid
        );
     }

    /**
     * Finish the vt_final with group-results and places
     */
    public function writeGroupResultsFinishAction()
    {
        $groupUid = $this->_request->getParam('uid', 0);

        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($groupUid)) {
            $this->_flashMessenger->addMessage('No group ID available', 'error');
            $this->redirect('/admin');
        }

        $summary = 0;
        $questions = (new Model_Questions())->getByConsultation($this->_consultation["kid"]);

        $votingFinal = new Model_VotingFinal();
        foreach ($questions as $question) {
            $finalVotes[$question['qi']] = $votingFinal->getFinalVotesByQuestion($question['qi'], $groupUid);

            foreach ($finalVotes as $values) {
                foreach ($values as $k => $v) {
                    $data['place'] = $k+1;
                    $data['tid']= $v['tid'];
                    $data['uid'] = $groupUid;
                    $votingFinal->updateFinalVotePlace($data);
                    $summary ++;
                }
            }
        }

        $this->_flashMessenger->addMessage(
            sprintf($this->view->translate('Data was created/updated.'), $summary),
            'success'
        );
        $this->redirect('/admin/close/index/kid/' . $this->_consultation["kid"]);
    }


    /**
     * Exports the voting results as csv
     */
    public function exportResultsAction()
    {
        if ($this->_consultation["kid"] == 0) {
            $this->_flashMessenger->addMessage('No consultation named.', 'error');
            $this->redirect('/admin');
        }
        $csv = '';

        // for str_replace because semicolons und quotation marks are unfavorable
        $search= array(";", "\"");
        $replace = array("#", "*");

        $consultation = (new Model_Consultations())->find($this->_consultation["kid"])->current()->toArray();
        if ($consultation) {
            $consultation['titl']= str_replace($search, $replace, $consultation['titl']);
            $consultation['titl'] = str_replace($search, $replace, $consultation['titl']);
            $csv .= '"Beteiligungsrunde: "' . $consultation['titl'];
        } else {
            $csv .= 'Beteiligungsrunde nicht gefunden!';
        }

        $votingResultsValues = (new Model_Votes())->getResultsValues($this->_consultation["kid"], $this->_question);
        if (!empty($votingResultsValues['currentQuestion'])) {
            $votingResultsValues['currentQuestion']['q']= str_replace(
                $search,
                $replace,
                $votingResultsValues['currentQuestion']['q']
            );
            $votingResultsValues['currentQuestion']['q'] = str_replace(
                $search,
                $replace,
                $votingResultsValues['currentQuestion']['q']
            );
            $csv.= ' - Frage: ' .$votingResultsValues['currentQuestion']['nr']
                . ' - ' . $votingResultsValues['currentQuestion']['q'] ."\r\n\r\n";
        } else {
            $csv .= 'Frage nicht gefunden!\r\n\r\n';
        }

        $csv.='"THESEN-ID";"THESE";"ERKLÄRUNG";"PLATZ";"PUNKTE GEWICHTET";"ANZAHL VOTES";"PUNKTE GESAMT"' . "\r\n";

        foreach ($votingResultsValues['votings'] as $value) {
            $value['thes'] = str_replace($search, $replace, $value['thes']);
            $value['expl'] = str_replace($search, $replace, $value['expl']);
            $csv.='"' . $value['tid'] . '";"'
                . $value['thes']. '";"'
                . $value['expl']. '";"'
                . $value['place'] . '";"'
                . $value['points'] . '";"'
                . $value['cast'] . '";"'
                . round($value['rank'], 2) . '"'. "\r\n";
        }

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        header("Content-type: text/csv");
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header(
            'Content-Disposition: attachment; filename=qid' . $this->_question
            . '_groupresults_' .  gmdate('Y-m-d_H') . '_utf-8.csv'
        );
        header('Pragma: no-cache');
        // @codingStandardsIgnoreLine
        echo html_entity_decode($csv, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Checks the qid
     * @param qid get param
     * @return int
     **/
    protected function getQid($params)
    {
        if (isset($params["qid"])) {
            $isDigit = new Zend_Validate_Digits();
            if ($params["qid"] > 0 && $isDigit->isValid($params["qid"])) {
                return (int) $params["qid"];
            } else {
                $this->_flashMessenger->addMessage('Invalid question id.', 'error');
                $this->_redirect('/admin/votingprepare/error');
            }
        }
    }
}
