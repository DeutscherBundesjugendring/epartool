<?php
class Admin_CloseController extends Zend_Controller_Action {

    protected $_flashMessenger = null;
    protected $_consultation = null;
    protected $_question = null;

    /**
     * @desc Construct
     * @return void
     */
    public function init()
    {
        // Setzen des Standardlayouts
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger =
                $this->_helper->getHelper('FlashMessenger');
        $this->initView();

        $this->_params = $this->_request->getParams();
        $this->_consultation = $this->getKid($this->_params);
        if (isset($this->_params["qid"])) {
            $this->_question = $this->getQid($this->_params);
        }

    }

    /**
     * indexAction()
     * @desc list links for finalize votes
     * @return void
     */
    public function indexAction()
    {
        $this->view->consultation = $this->_consultation;
        $votingRightsModel = new Model_Votes_Rights();

        $votingRights = $votingRightsModel->getByConsultation($this->_consultation["kid"]);

        //group resultes stored in DB vt_final?
        $VotingFinalModel= new Model_VotingFinal();
        $resultswritten= $VotingFinalModel -> isGroupResultWritten($this->_consultation["kid"]);

        foreach ($votingRights as $key=>$value) {
            array_key_exists($value['uid'], $resultswritten) ? $votingRights[$key]['vt_finalized']   = "y" : $votingRights[$key]['vt_finalized']   = "n";
        }

        $this->view->votingRights = $votingRights;

    }

    /**
     * anonymizeVotesAction()
     * @desc delete votes from vt_iniv by consultion an redirects to index action
     * @return
     */
     public function anonymizeVotesAction() {

        $records = 0;
        $inputs = array();

        // gets the input ids
        $inputsModel = new Model_Inputs();
        $inputs = $inputsModel -> getVotingchain($this->_consultation["kid"]);
        $inputs = $inputs['tid'];

        $votesIndivModel = new Model_Votes_Individual();
        foreach ($inputs  as $value) {
        $result = 0;
        $result = $votesIndivModel->anonymizeVotes($value);
        $records = $records+$result;
        }

        $consultationModel = new Model_Consultations;
        $closedata = array();
        $closedata['vt_anonymized'] = "y";
        $consultationModel->updateById($this->_consultation["kid"], $closedata);

        $message = sprintf(
            $this->view->translate('Es wurden %s Datensätze gelöscht. Die Abstimmung ist somit anonymisiert!'),
            $records
        );
        $this->_flashMessenger->addMessage($message, 'success');
         $this->redirect('/admin/close/index/kid/' . $this->_consultation["kid"]);
     }

      /**
     * writeResultsAction()
     * prepare votingresults from vt_indiv an write in vt_final in x steps
     * @return
     *
     **/
     public function writeResultsAction() {

        $inputs = array();
        $data = array();

        $steps=50;
        $page = $this->_request->getParam('page', 0);
        $pages = $this->_request->getParam('pages', 0);

        $this->_flashMessenger->addMessage("Die Daten werden geschrieben, Bitte warten Sie!", 'success');

        // gets the input ids
        $inputsModel = new Model_Inputs();
        $inputs = $inputsModel -> getVotingchain($this->_consultation["kid"]);
        $inputs = $inputs['tid'];

        // vars for redirecting
        $sumInputs = count($inputs);
        $pages=round(($sumInputs/$steps),0);

        //extract 20 records
        $inputs = array_slice ( $inputs ,($page*$steps), $steps);
        $page = $page+1; //$page++ doesn't work!?

        //initialise required models
        $votesIndivModel = new Model_Votes_Individual();
        $followUpModel = new Model_FollowupsRef ();
        $votingFinal = new Model_VotingFinal();
        $votesRightsModel = new Model_Votes_Rights();

        // get the voting weights
        $votingWeights = $votesRightsModel->getWeightsByConsultation($this->_consultation["kid"]);

        //get and prepare the data for each input, write in vt_final
        foreach ($inputs as $key => $value) {
            $data = $votesIndivModel->getVotingValuesByThesis($value, $this->_consultation["kid"],  $votingWeights);
            $data['kid'] =$this->_consultation["kid"];
            $data['tid'] = $value;
            $data['fowups'] = 'n';
            $followUps = $followUpModel ->  getFollowupCountByTids($value);
            if (isset($followUps[$value])){
                $data['fowups'] = 'y';
            }
            $result = $votingFinal->addOrUpdateFinalVote($data);
         }
        // so often as needed ... when finish go to the writeResultsFinishAction()
        if ($page <= $pages) {
            $this->redirect('/admin/close/write-results/kid/' . $this->_consultation["kid"].'/page/'.$page.'/pages/'.$pages);
            $this->view->consultation = $this->_consultation;
        } else {
            $this->redirect('/admin/close/write-results-finish/kid/' . $this->_consultation["kid"].'/suminputs/'.$sumInputs);
        }

     }


    /**
     * writeResultsFinishAction ()
     * finish the vt_final with the places, updates the consultation table  and says i am ready
     *
     **/
    public function writeResultsFinishAction() {

        $questions = array();
        $finalVotes = array();
        $data= array();
        $data_inputs= array();

        $sumInputs = $this->_request->getParam('suminputs', 0);

        // get the questions
        $questionModel = new Model_Questions();
        $questions = $questionModel->getByConsultation($this->_consultation["kid"]);

        // get the sorted vt_final data for each question (rank ASC cast DESC)
        $votingFinal = new Model_VotingFinal();
        foreach ($questions as $question) {
            $finalVotes[$question['qi']] = $votingFinal -> getFinalVotesByQuestion($question['qi']);
        }

        $inputsModel = new Model_Inputs();
        // update the place column in vt_final
        // update the place and votes column in inpt
        foreach ($finalVotes as $key=>$value) {
            $data= array();
            foreach ($value as $k=>$v) {
                $data['place'] = $k+1;
                $data['tid']= $v['tid'];
                $data_inputs = $data;
                $data_inputs['votes'] = $v['cast'];  // casts = summary votes its in use?? if not delete this and update only the place in inpt table
                $votingFinal->updateFinalVotePlace($data);
                $inputsModel->updateById($v['tid'],$data_inputs);

            }
        }

         $consultationModel = new Model_Consultations;
        $closedata = array();
        $closedata['vt_finalized'] = "y"; //set flag its finalized!
        $consultationModel->updateById($this->_consultation["kid"], $closedata);
        $message = sprintf($this->view->translate('Es wurden %s Datensätze erstellt/aktualisiert'), $sumInputs);
        $this->_flashMessenger->addMessage($message, 'success');
         $this->redirect('/admin/close/index/kid/' . $this->_consultation["kid"]);
     }

     /**
     * writeGroupResultsAction()
     * prepare votingresults from vt_indiv an write in vt_final for groups
     * @return
     *
     **/
     public function writeGroupResultsAction() {

        $groupUid = $this->_request->getParam('uid', 0);

        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($groupUid)) {
            $this->_flashMessenger->addMessage('Keine GruppenID vorhanden', 'error');
            $this->redirect('/admin');
        }

        $this->_flashMessenger->addMessage("Die Daten werden geschrieben, Bitte warten Sie!", 'success');

        // gets the input ids
        $inputsModel = new Model_Inputs();
        $inputs = $inputsModel -> getVotingchain($this->_consultation["kid"]);

        $votesIndivModel = new Model_Votes_Individual();
        $votingFinal = new Model_VotingFinal();

        // get and write votingresults for every group and input
        foreach ($inputs['tid'] as $k=>$v)  {
                $tid = $v;
                $data=array();

                    $data = $votesIndivModel->getVotingValuesByGroupAndThesis($tid, $this->_consultation["kid"], $groupUid);
                    $result = $votingFinal->addOrUpdateFinalVote($data);

        }
        $this->redirect('/admin/close/write-group-results-finish/kid/' . $this->_consultation["kid"].'/uid/'.$groupUid);
     }


    /**
     * writeGroupResultsFinishAction ()
     * finish the vt_final with group-results and places
     *
     **/
    public function writeGroupResultsFinishAction() {

        $groupUid = $this->_request->getParam('uid', 0);

        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($groupUid)) {
            $this->_flashMessenger->addMessage('Keine GruppenID vorhanden', 'error');
            $this->redirect('/admin');
        }

        $summary = 0;
        // get the questions
        $questionModel = new Model_Questions();
        $questions = $questionModel->getByConsultation($this->_consultation["kid"]);

        $votingFinal = new Model_VotingFinal();


            foreach ($questions as $question) {
                $finalVotes[$question['qi']] = $votingFinal -> getFinalVotesByQuestion($question['qi'],$groupUid );

                foreach ($finalVotes as $keys=>$values) {
                    foreach ($values as $k=>$v) {
                    $data['place'] = $k+1;
                    $data['tid']= $v['tid'];
                    $data['uid'] = $groupUid;
                    $votingFinal->updateFinalVotePlace($data);
                    $summary ++;
                }

            }
        }

        $message = sprintf($this->view->translate('Es wurden Datensätze erstellt/aktualisiert'), $summary);
        $this->_flashMessenger->addMessage($message, 'success');
         $this->redirect('/admin/close/index/kid/' . $this->_consultation["kid"]);
    }


    /**
     * exportResultsAction()
     *  exports the voting results as csv
     *
     **/
    public function exportResultsAction() {

        if ($this->_consultation["kid"] == 0) {
            $this->_flashMessenger->addMessage('Keine Beteiligungsrunde angegeben.', 'error');
            $this->redirect('/admin');
        }
        $csv="";

        // for str_replace because semicolons und quotation marks are unfavorable
        $search= array(";", "\"");
        $replace = array("#", "*");


        $consultationModel = new Model_Consultations();
        $consultation = $consultationModel->find($this->_consultation["kid"] )->current()->toArray();
        if (!empty($consultation)) {
            $consultation['titl']= str_replace($search, $replace, $consultation['titl']);
            $consultation['titl'] = str_replace($search, $replace, $consultation['titl']);
            $csv.= '"Beteiligungsrunde: "' . $consultation['titl'];
        } else {
            $csv.= 'Beteiligungsrunde nicht gefunden!';
        }

        $votesModel = new Model_Votes();
        $votingResultsValues = $votesModel->getResultsValues($this->_consultation["kid"], $this->_question);

         if (!empty($votingResultsValues['currentQuestion'])) {
             $votingResultsValues['currentQuestion']['q']= str_replace($search, $replace, $votingResultsValues['currentQuestion']['q']);
            $votingResultsValues['currentQuestion']['q'] = str_replace($search, $replace, $votingResultsValues['currentQuestion']['q']);
            $csv.= ' - Frage: ' .$votingResultsValues['currentQuestion']['nr'] . ' - ' . $votingResultsValues['currentQuestion']['q'] ."\r\n\r\n";
        } else {
            $csv.= 'Frage nicht gefunden!\r\n\r\n';
        }

        $csv.='"THESEN-ID";"THESE";"ERKLÄRUNG";"PLATZ";"PUNKTE GEWICHTET";"ANZAHL VOTES";"PUNKTE GESAMT"' . "\r\n";

        foreach ($votingResultsValues['votings'] as $key => $value) {

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

        // disable layout and view
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // set Headers
        header("Content-type: text/csv");
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Content-Disposition: attachment; filename=qid'.$this->_question . '_groupresults_'. gmdate('Y-m-d_H') . '_utf-8.csv');
        header('Pragma: no-cache');
        echo html_entity_decode($csv, ENT_COMPAT, 'UTF-8');

    }

    /**
     * getKid
     * checks the kid and returns the values from DB if the consultation exists
     * @param get param kid
     * @return variables from consultation or votingprepare error
     *
     **/
    protected function getKid($params)
    {
        if (isset($params["kid"])) {
            $isDigit = new Zend_Validate_Digits();

            if ($params["kid"] > 0 && $isDigit->isValid($params["kid"])) {
                $consultationModel = new Model_Consultations();
                $this->_consultation = $consultationModel->getById($params["kid"]);
                if (count($this->_consultation) == 0) {
                    $this->_flashMessenger->addMessage('keine Beteiligungsrunde zu dieser ID vorhanden', 'error');
                    $this->_redirect('/admin/close/error');
                } else {
                    return $this->_consultation;
                }
            } else {
                $this->_flashMessenger->addMessage('ID der Beteiligungsrunde ungültig', 'error');
                $this->_redirect('/admin/close/error');
            }
        }
    }

    /**
     * getQid
     * checks the qid
     * @param qid, get param
     * @return (int)
     *
     **/
    protected function getQid($params)
    {
        if (isset($params["qid"])) {
            $isDigit = new Zend_Validate_Digits();
            if ($params["qid"] > 0 && $isDigit->isValid($params["qid"])) {
                return (int) $params["qid"];
            } else {
                $this->_flashMessenger->addMessage('QuestionID ungültig', 'error');
                $this->_redirect('/admin/votingprepare/error');
            }
        }
    }

}
?>
