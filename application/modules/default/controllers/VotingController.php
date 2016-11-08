<?php

class VotingController extends Zend_Controller_Action
{
    protected $_user = null;
    protected $_consultation = null;
    protected $_flashMessenger = null;

    /**
     * Construct
     */
    public function init()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $consultationModel = new Model_Consultations();
        $consultation = $consultationModel->find($kid)->current();
        if ($consultation) {
            $this->_consultation = $consultation;
            $this->view->consultation = $consultation;
        } else {
            $this->_redirect('/');
        }
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    /**
     * @param session params
     * @return bool or redirect
     */
    private function getVotingRightsSession()
    {
        $votingRightsSession = new Zend_Session_Namespace('votingRights');
        if ($votingRightsSession->access != $this->_consultation->kid) {
            $this->_flashMessenger->addMessage('This participation round is currently not open for voting.', 'error');
            $this->redirect('/');
        }

        return $votingRightsSession;
    }

    /**
     * @param kid
     * @return array
     */
    private function getVotingSettings()
    {
        $kid = $this->_consultation->kid;
        $settingsModel = new Model_Votes_Settings();
        $votingSettings = $settingsModel->getById($kid);

        return $votingSettings;
    }

    /**
     * getVotingbasket() get SuperVotes from current User
     * @param kid
     * @return array
     */
    private function getVotingBasket($subUid)
    {
           $votingBasket = array();
           $votingIndividualModel = new Model_Votes_Individual();

           $votingBasket["countvotes"] = $votingIndividualModel->countParticularImportantVote($subUid);
           $votingBasket["votes"] = $votingIndividualModel->getParticularImportantVote($subUid);

           return $votingBasket;
    }


    /**
     * @param $this->_consultation from init()
     * @return redirect
     */
    public function checkVotingDate()
    {
        $nowDate = Zend_Date::now();

        if ($nowDate->isEarlier(new Zend_Date($this->_consultation->vot_fr, Zend_Date::ISO_8601))) {
            $this->_flashMessenger->addMessage('It is not possible to vote at the moment.', 'info');
            $this->redirect('/');
        } elseif ($nowDate->isLater(new Zend_Date($this->_consultation->vot_to, Zend_Date::ISO_8601))
            && $this->_consultation->vot_to != '0000-00-00 00:00:00'
            && $this->_consultation->vot_res_show == 'y'
        ) {
            $this->_flashMessenger->addMessage('The Voting is finished. You can check the results below.', 'info');
            $this->redirect('/voting/results/kid/' . $this->_consultation->kid);
        }
    }

    /**
     * form for access for voting, check email and authcode
     */
    public function indexAction()
    {
        $votingRightsSession = new Zend_Session_Namespace('votingRights');
        $form = new Default_Form_Voting_Authentification();
        // check if voting is in time
        $this->checkVotingDate();
        // if session is allready created, forword to overview
        if ($votingRightsSession->access == $this->_consultation->kid) {
            $this->redirect('/voting/overview/kid/' . $this->_consultation->kid . '#voting');
        } elseif ($this->_request->isPost()) {  // request sended
            $data = $this->_request->getPost();
            // if form is valid
            if ($form->isValid($data)) {
                $emailAddress = $this->getRequest()->getParam('email');
                $authcode = $this->getRequest()->getParam('authcode');
                $votingRightModel = new Model_Votes_Rights();
                $votingRights = $votingRightModel->findByCodeWithUserData($authcode);
                // check if votingcode is correct
                if (!empty($votingRights)
                    && ($this->_consultation['allow_groups'] || $votingRights['email'] === $emailAddress)
                ) {
                    if ($votingRights['kid'] == $this->_consultation->kid) {
                        $votingGroupModel = new Model_Votes_Groups();
                        $votingSubuser = $votingGroupModel->getByEmail(
                            $emailAddress,
                            $votingRights['uid'],
                            $this->_consultation->kid
                        );

                        if (empty($votingSubuser)) {
                            // create list of all votable inputs
                            $subUid = md5($emailAddress . $this->_consultation->kid);
                            // save subuser
                            $data = array(
                                'uid'=>$votingRights['uid'],
                                'sub_user'=>$emailAddress,
                                'sub_uid'=>$subUid,
                                'kid'=>$this->_consultation->kid,
                                'member'=>'u'
                            );
                            if (!$votingGroupModel->add($data)) {
                                throw new Exception('Fehler im Abstimmung. Bitte kontaktieren Sie den Administrator.');
                            }
                        } else {
                            if ($votingSubuser['member'] === 'n') {
                                $this->_flashMessenger->addMessage('Access to this account has been denied by Group Leader.', 'error');
                                $this->redirect('/input/index/kid/' . $this->_consultation->kid);
                            }
                            $subUid = $votingSubuser['sub_uid'];
                        }
                        // access
                        $votingRightsSession->access = $this->_consultation->kid;
                        // authcode
                        $votingRightsSession->vtc = $authcode;
                        // subuid
                        $votingRightsSession->subUid = $subUid;
                        $votingRightsSession->uid = $votingRights['uid'];
                        $votingRightsSession->weight = $votingRights['vt_weight'];
                        // all is correct, redirect to overview
                        $this->redirect('/voting/overview/kid/' . $this->_consultation->kid . '#voting');
                    } else {
                        $this->_flashMessenger->addMessage('Your data are not correct. Please check again.', 'error');
                        $form->populate($data);
                    }
                } else { // no access for voting
                    $this->_flashMessenger->addMessage('Your data are not correct. Please check again.', 'error');
                    $form->populate($data);
                }
            } else { // invalid form
                $this->_flashMessenger->addMessage('Your data are not correct. Please check again.', 'error');
                $form->populate($data);
            }
        } else {       // Check if user comes from email with authcode
            $authcode = $this->getRequest()->getParam('authcode');
            if (!empty($authcode)) {
                // pre-set value of authcode
                $authcodeElement = $form->getElement('authcode');
                $authcodeElement->setValue($authcode);
            }
        }
        $this->view->votingExplanation = html_entity_decode($this->_consultation->vot_expl, ENT_COMPAT, 'UTF-8');
        $this->view->authform = $form;
        $this->view->consultationTitle = $this->_consultation->titl;
    }

    /**
     * Overview of all questions and tags
     * note: need authentification over session
     */
    public function overviewAction()
    {
        // no access, redirect back
        $votingRightsSession = $this->getVotingRightsSession();
        $kid = $this->_consultation->kid;
        // Questions
        $questionModel = new Model_Questions();
        $this->view->questions = $questionModel->getByConsultation($kid);
        // Tags for Tagcloud
        $tagModel = new Model_Tags();
        $this->view->tags = $tagModel->getAllByConsultation($kid, 'y');

        // count of votable inputs
        $inputModel = new Model_Inputs();
        $filter = array(array(
            'field'=>'vot',
            'operator'=>'=',
            'value'=>'y'
        ));
        $this->view->votableInputs = $inputModel->getCountByConsultationFiltered($kid, $filter);
        // count of voted inputs
        $votingIndivModel = new Model_Votes_Individual();
        $this->view->votedInputs = $votingIndivModel->countVotesBySubuser($votingRightsSession->subUid);
    }

    /**
     * Overview of all questions and inputs (include votes by user)
     * note: need authentification over session
     * @author Karsten Tackmann
     */
    public function previewAction()
    {
        // no access, redirect back
        $votingRightsSession = $this->getVotingRightsSession();
        $this->view->settings = $this->getVotingSettings();

        $subUid = $votingRightsSession->subUid;
        $kid = $this->_consultation->kid;

        // Questions
        $questionModel = new Model_Questions();
        $questions = $questionModel->getByConsultation($kid)->toArray();
        $questionResult = array();

        // inputs per question with uservoting
        $votingUserInputModel = new Model_Votes_Uservotes();

        $i = 0;
        foreach ($questions as $question) {
            $questionID = $question['qi'];
            $questionResult["$i"] = $question;
            $questionResult["$i"]['QuestionsAndInputs'] = $votingUserInputModel->fetchAllInputsWithUserVotes(
                $questionID,
                $subUid,
                $kid
            );
            $i++;
        }

        $this->view->questions = $questionResult;

        // count of votable inputs
        $filter = array( array('field' => 'vot', 'operator' => '=', 'value' => 'y'));

        $inputModel = new Model_Inputs();
        $this->view->votableInputs = $inputModel->getCountByConsultationFiltered($kid, $filter);
        // count of voted inputs
        $votingIndivModel = new Model_Votes_Individual();
        $this->view->votedInputs = $votingIndivModel->countVotesBySubuser($votingRightsSession->subUid);
        $this->view->videoServicesStatus = (new Model_Projects())
            ->find((new Zend_Registry())->get('systemconfig')->project)->current();

    }

    /**
     * ajaxresponse from previewAction
     * note: need authentification over session
     * @author Karsten Tackmann
     */
    public function previewfeedbackAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            exit; //no AjaxRequest
        }

        $this->_helper->layout()->disableLayout();
        $votingRightsSession = $this->getVotingRightsSession();

        $this->view->settings = $this->getVotingSettings();

        $param = $this->getRequest()->getParams();
        $pts = (isset($param['points']) ? (int) $param['points'] : null);

        if ($pts < Service_Voting::POINTS_MIN || $pts > Service_Voting::POINTS_MAX) {
            $this->view->error = "1";
            $this->view->error_comment = $this->view->translate('Your rating is out of accepted range.');

            return;
        }

        $subUid = $votingRightsSession->subUid;
        $uid = (int) $votingRightsSession->uid;
        $kid = $this->_consultation->kid;
        $tid = (int)$param['id'];

        // check wheter the thesisID is correct
        if (!(new Model_Inputs())->thesisExists($tid, $kid)) {
            $this->view->error = "1";
            $this->view->error_comment = $this->view->translate('Contribution not found.');
            return;
        }

        if (empty($votingRightsSession->confirmationHash)) {
            $votingRightsSession->confirmationHash = (new Service_Voting())->generateConfirmationHash();
        }

        $votingSuccess = (new Model_Votes_Individual())->updateVote(
            $tid,
            $subUid,
            $uid,
            $pts,
            $votingRightsSession->confirmationHash
        );

        if (!$votingSuccess) {
            $this->view->error = "1";
            $this->view->error_comment = $this->view->translate('An error occured.');
            return;
        } else {
            $feedback = array(
                'points' => $votingSuccess['points'],
                'pimp' => $votingSuccess['pimp'],
                'tid' => $tid,
                'kid' => $kid
            );
            $this->view->feedback = $feedback;
        }

    }

    /**
     * Ajaxresponse from previewAction by click the particular important button
     */
     public function previewfeedbackpiAction()
     {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            exit; //no AjaxRequest
        }

        $this->_helper->layout()->disableLayout();
        $votingRightsSession = $this->getVotingRightsSession();

        $this->view->settings = $this->getVotingSettings();

        if ($this->view->settings['btn_important'] == 'n') {
            $this->view->error = "1";
            $this->view->error_comment = $this->view->translate('Using of superbutton is not allowed.');
            return;
        }

        // count max possibility click on particularly important button
        // returns comment for user or action and return buttons
        $kid = $this->_consultation->kid;
        $param = $this->getRequest()->getParams();
        $tid = (int) $param['id'];

        $votingIndividualModel = new Model_Votes_Individual();

        // check wheter the thesisID is correct
        if (!(new Model_Inputs())->thesisExists($tid, $kid)) {
            $this->view->error = "1";
             $this->view->error_comment = $this->view->translate('Contribution not found.');
        }

        if (empty($votingRightsSession->confirmationHash)) {
            $votingRightsSession->confirmationHash = (new Service_Voting())->generateConfirmationHash();
        }

        $votingSuccess = $votingIndividualModel->updateParticularImportantVote(
            $tid,
            $votingRightsSession->subUid,
            (int) $votingRightsSession->uid,
            $this->view->settings['btn_numbers'],
            $this->view->settings['btn_important_factor'],
            $this->view->settings['btn_important_max'],
            $votingRightsSession->confirmationHash
        );

        if (isset ($votingSuccess['points'])) {
            $feedback = array(
                'points' => $votingSuccess['points'],
                'tid' => $tid,
                'pimp' => $votingSuccess['pimp'],
                'kid' => $kid
            );
        } elseif (isset($votingSuccess['max'])) {
            $this->view->error = "1";
            $this->view->error_comment = 'The Super Button allows you to value a limited number of contributions'
                . ' higher. Change previous votings and make room for more important contributions!';
            $currentVote = $votingIndividualModel->getCurrentVote($tid, $votingRightsSession->subUid);
            $feedback = array(
                'points' => $currentVote['pts'],
                'tid' => $tid,
                'pimp' => $currentVote['pimp'],
                'kid' => $kid
            );
        } else {
            $this->view->error = "1";
            $feedback = array();
        }

        $this->view->feedback = $feedback;
    }

    /**
     * ajaxresponse for remove Supervote from basket and save the next lower level
     * @author Karsten Tackmann
     */
     public function removethesisAction()
     {

        if(!$this->getRequest()->isXmlHttpRequest()) {
            exit; //no AjaxRequest
        }

        $this->_helper->layout()->disableLayout();

        $pts = 0;

        $votingRightsSession = $this->getVotingRightsSession();
        $subUid = $votingRightsSession->subUid;
        $uid = (int) $votingRightsSession->uid;
        $param = $this->getRequest()->getParams();
        $tid= (int) $param['tid'];

        // check wheter the thesisID is correct
        if (!(new Model_Inputs())->thesisExists($tid, $this->_consultation->kid)) {
            $this->_flashMessenger->addMessage('Such contribution does not exist!', 'error');
            $this->redirect('/voting/vote/kid/' . $this->_consultation->kid);
        }

        if (empty($votingRightsSession->confirmationHash)) {
            $votingRightsSession->confirmationHash = (new Service_Voting())->generateConfirmationHash();
        }

        // next lower level
        $votingsettings =  $this->getVotingSettings();
        $pts = $votingsettings['btn_numbers'];
        $votingSuccess = (new Model_Votes_Individual())->updateVote(
            $tid,
            $subUid,
            $uid,
            $pts,
            $votingRightsSession->confirmationHash
        );

        if (!$votingSuccess) {
            $this->view->response = "error";
        } else {
            $this->view->response = "success";
        }
     }


    public function filterStatements($questionResult)
    {
        $questionResultVoted = array();
        $questionResultUnVoted = array();

        foreach ($questionResult as $value) {
            (!empty($value["status"]))
                ? ($questionResultVoted[$value['tid']] = $value)
                : $questionResultUnVoted[$value['tid']] = $value ; //use for quick fix the backbutton and votingcount!!
        }

        return [
            'questionResultUnVoted' => $questionResultUnVoted,
            'questionResultVoted' => $questionResultVoted
        ];
    }

    /**
     * Voting filtered by tags or questions
     * note: need authentification over session
     */
    public function voteAction()
    {
        $votingRightsSession = $this->getVotingRightsSession();

        $kid = (int) $this->_consultation->kid;
        $qid = (int) $this->getRequest()->getParam('qid');
        $tagId = (int) $this->getRequest()->getParam('tag');
        $tid =(int) $this->getRequest()->getParam('tid');
        $subUid = $votingRightsSession->subUid;
        $uid = (int) $votingRightsSession->uid;  //use for quick fix the back button !!

        $votingIndividualModel = new Model_Votes_Individual();

        //use for fix the back button begin !!
        if (!empty($tid)) {
            // check if the thesis id are correct
            if (!(new Model_Inputs())->thesisExists($tid, $this->_consultation->kid)) {
                $this->_flashMessenger->addMessage('Such contribution does not exist!', 'error');
                $this->redirect('/voting/vote/kid/' . $this->_consultation->kid);
            } else {
                $votingIndividualModel->deleteParticularImportantVote($uid, $subUid, $tid);
            }

        }
        //use for fix the back button end !!

        if (empty($qid) && empty($tagId)) {
            $this->redirect('/voting/overview/kid/'.$kid);
        }

        $votingUserInputModel = new Model_Votes_Uservotes();
        $questionResult = (!empty($qid))
            ?  $votingUserInputModel->fetchAllInputsWithUserVotes($qid, $subUid, $kid)
            :  $votingUserInputModel->fetchAllInputsWithUserVotes(null, $subUid, $kid, $tagId);

        // votes inputs and unvoted inputs
        $questionResultSeparated = $this->filterStatements($questionResult);
        $thesesVoted = $questionResultSeparated['questionResultVoted'];
        $thesesUnVoted = $questionResultSeparated['questionResultUnVoted'];

        $question = null;
        if (count($thesesUnVoted) === 0) {
            $this->view->noMoreThesis = true;
        } else {
            //use for quick fix the back button begin !!
            if (empty($tid)) {
                $randKeys = array_rand($thesesUnVoted, 1);
                $thesis = $thesesUnVoted[$randKeys];
            } else { // back button is in use
                $thesis = $thesesUnVoted[$tid];
            }
            //use for quick fix the back button end !!

            // Check last voted thesis and append to view
            $lastTid = $votingIndividualModel->getLastBySubuser($subUid);
            if (!empty($lastTid) && $thesis['tid'] !== $lastTid[0]['tid']) {
                $undoLinkParameters = ['kid' => $kid, 'tid' => $lastTid[0]['tid']];
                if (!empty($tagId)) {
                    $undoLinkParameters['tag'] = $tagId;
                } else {
                    $undoLinkParameters['qid'] = $lastTid[0]['qi'];
                }
                $this->view->undoLinkParameters = $undoLinkParameters;
            }

            $question = (new Model_Questions())->getById($thesis['qi']);
            $this->view->thesis = $thesis;
            $this->view->question = $question;

            $skipLinkParameters = ['kid' => $kid, 'token' => time()];
            if (!empty($tagId)) {
                $skipLinkParameters['tag'] = $tagId;
            } else {
                $skipLinkParameters['qid'] = $qid;
            }
            $this->view->skipLinkParameters = $skipLinkParameters;
        }

        $this->view->videoServicesStatus = (new Model_Projects())
            ->find((new Zend_Registry())->get('systemconfig')->project)->current();
        $this->view->thesesCount = count($questionResult);
        $this->view->thesesCountVoted = count($thesesVoted);
        $this->view->settings = $this ->getVotingSettings();
        $this->view->votingBasket= $this ->getVotingBasket($subUid);

        if ($question && $question['vot_q']) {
            $votingQuestion = $question['vot_q'];
        } else {
            $project = (new Model_Projects())->find(Zend_Registry::get('systemconfig')->project)->current();
            $votingQuestion = $project['vot_q'];
        }
        $this->view->defaultVoteQuestion = $votingQuestion;
    }

    /**
     * Saves the voting values for normal button
     * Redirects to voteAction();
     */
    public function thesisvoteAction()
    {
        // no access, redirect back
        $votingRightsSession = $this->getVotingRightsSession();
        // no view and layout
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $param = $this->getRequest()->getParams();
        $backParam = (!empty($param['qid'])) ? '/qid/' . $param['qid'] : '/tag/' . $param['tag'];
        $pts = (isset($param['pts']) ? (int) $param['pts'] : null);
        $subUid = $votingRightsSession->subUid;
        $uid = $votingRightsSession->uid;
        $tid = (int)$param['tid'];

        // check if the thesisid are correct
        if (!(new Model_Inputs())->thesisExists($tid, $this->_consultation->kid)) {
            $this->_flashMessenger->addMessage('Such contribution does not exist!', 'error');
            $this->redirect('/voting/vote/kid/' . $this->_consultation->kid);
        }

        // check if a tid is given
        if (empty($tid) || (empty($param['qid']) && empty($param['tag']))) {
            $this->_flashMessenger->addMessage('Something went wrong.', 'info');
            $this->redirect('/voting/overview/kid/'.$this->_consultation->kid);
        }

        // check if the points are correct
        if ($pts > Service_Voting::POINTS_MAX || $pts < Service_Voting::POINTS_MIN) {
            $this->_flashMessenger->addMessage('The points are not correct.', 'info');
            $this->redirect('/voting/vote/kid/' . $this->_consultation->kid);
        }

        $votingService = new Service_Voting();

        if (empty($votingRightsSession->confirmationHash)) {
            $votingRightsSession->confirmationHash = $votingService->generateConfirmationHash();
        }

        try {
            $votingService->saveVote(
                ['tid' => $tid, 'uid' => $uid, 'sub_uid' => $subUid, 'pts' => $pts],
                $votingRightsSession->confirmationHash
            );

            $this->_flashMessenger->addMessage('Your vote has been counted!', 'info');
            $this->redirect('/voting/vote/kid/' . $this->_consultation->kid . $backParam);
        } catch (Dbjr_Voting_Exception $ex) {
            $this->_flashMessenger->addMessage('Your vote could not be registered. (1)', 'info');
            $this->redirect('/voting/vote/kid/' . $this->_consultation->kid . '/tid/' . $param['tid'] . $backParam);
        }
    }

    /**
     * Saves the values for the superbutton
     * Redirects to voteAction();
     */
    public function thesissupervoteAction()
    {
        // no access, redirect back
        $votingRightsSession = $this->getVotingRightsSession();
        // no view and layout
        $this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $this -> settings = $this -> getVotingSettings();

        $param = $this -> getRequest() -> getParams();
        $backParam = (!empty($param['qid'])) ? '/qid/' . $param['qid'] : '/tag/' . $param['tag'];
        $pts = (string) $param['pts'];
        $tid = (int)$param['tid'];

        if ($this->settings['btn_important'] == 'n') {
            $this->_flashMessenger -> addMessage('Clicking the superbutton is not allowed.', 'info');
            $this->redirect('/voting/vote/kid/' . $this->_consultation->kid);
            return;
        }

        // check if the points are correct
        if ($pts != 'y') {
            $this->_flashMessenger -> addMessage('The points are not correct.', 'info');
            $this->redirect('/voting/vote/kid/' . $this->_consultation->kid);
        }

        $votingIndividualModel = new Model_Votes_Individual();
        // check if the thesisid are correct
        if (!(new Model_Inputs())->thesisExists($tid, $this->_consultation->kid)) {
            $this->_flashMessenger->addMessage('Such contribution does not exist!', 'error');
            $this->redirect('/voting/vote/kid/' . $this->_consultation->kid);
        }

        // check if a tid is given
        if (empty($tid) || (empty($param['qid']) && empty($param['tag']))) {
            $this -> _flashMessenger -> addMessage('Please choose a question or keyword.', 'info');
            $this -> redirect('/voting/overview');
        }

        if (empty($votingRightsSession->confirmationHash)) {
            $votingRightsSession->confirmationHash = (new Service_Voting())->generateConfirmationHash();
        }

        $votingSuccess = $votingIndividualModel->updateParticularImportantVote(
            $tid,
            $votingRightsSession -> subUid,
            (int) $votingRightsSession -> uid,
            $this -> settings['btn_numbers'],
            $this -> settings['btn_important_factor'],
            $this -> settings['btn_important_max'],
            $votingRightsSession->confirmationHash
        );

        if (!$votingSuccess) {
            $this->_flashMessenger->addMessage('Your vote could not be registered. (1)', 'info');
            $this->redirect('/voting/vote/kid/' . $this -> _consultation -> kid . '/tid/' . $tid . $backParam);

        } elseif (!isset($votingSuccess['max'])) {
            $this->_flashMessenger -> addMessage('Your vote has been counted.', 'info');
            $this->redirect('/voting/vote/kid/' . $this -> _consultation -> kid . $backParam);
        }
        if (isset($votingSuccess['max'])) {
            $this->_flashMessenger->addMessage(
                'The Super Button allows you to value a limited number of contributions higher.'
                    . ' Change previous votings and make room for more important contributions!',
                'info'
            );
            $this->redirect('/voting/preview/kid/' . $this -> _consultation -> kid. $backParam);
        }
    }

    /**
     * User stop the voting, if user member of group, send confirm-email to this user
     */
    public function stopvotingAction()
    {
        $votingRightsSession = new Zend_Session_Namespace('votingRights');
        $uid = $votingRightsSession->uid;
        $subUid = $votingRightsSession->subUid;

        $votingGroup = new Model_Votes_Groups();
        $subUser = $votingGroup->getByUser($uid, $subUid, $this->_consultation['kid']);

        if (empty($subUser)) {
            $votingRightsSession->unsetAll();
            $this->_flashMessenger->addMessage('User could not be found.', 'error');
            $this->redirect('/voting/preview/kid/' . $this->_consultation['kid']);
        }

        if (empty($votingRightsSession->confirmationHash)) {
            $this->_flashMessenger->addMessage('No votes to process.', 'error');
            $this->redirect('/voting/preview/kid/' . $this->_consultation['kid']);
        }

        $votingService = new Service_Voting();
        try {
            if ($votingService->stopVoting(Zend_Auth::getInstance(), $votingRightsSession->confirmationHash)) {
                $subUser = (new Model_Votes_Groups())->find($uid, $subUid, $this->_consultation['kid'])->current();
                if (!$subUser) {
                    $this->_flashMessenger->addMessage('Voter not found.', 'error');
                    $this->redirect('/input/index/kid/' . $this->_consultation['kid']);
                }
                $this->view->groupmember = $subUser['sub_user'];
            }
        } catch (Dbjr_Voting_NoVotesException $e) {
            $this->_flashMessenger->addMessage('No votes to handle.', 'error');
            $this->redirect('/input/index/kid/' . $this->_consultation['kid']);
        } catch (Dbjr_Voting_MissingGroupLeaderException $e) {
            $this->_flashMessenger->addMessage('Cannot find group leader.', 'error');
            $this->redirect('/input/index/kid/' . $this->_consultation['kid']);
        } catch (Dbjr_Voting_MissingVotingGroupException $e) {
            $this->_flashMessenger->addMessage('Cannot find voting group.', 'error');
            $this->redirect('/input/index/kid/' . $this->_consultation['kid']);
        } catch (Dbjr_Voting_MissingVotingRightsException $e) {
            $this->_flashMessenger->addMessage('No voting rights found.', 'error');
            $this->redirect('/input/index/kid/' . $this->_consultation['kid']);
        }

        unset($votingRightsSession->confirmationHash);
        $votingRightsSession->unsetAll();
    }

    /**
     * user confirm his own votes
     * if user is unconfirmed, send E-Mail to groupleader
     * action have to be "acc" or "rej"
     * user have to be given (user=subuid)
     * authcode have to be given (authcode)
     */
    public function confirmvotingAction()
    {
        $confirmationHash = $this->getRequest()->getParam('hash');

        if (empty($confirmationHash)) {
            $this->_flashMessenger->addMessage('The link is not correct.', 'error');
            $this->redirect('/');
        }

        $votingService = new Service_Voting();
        $dbAdapter = (new Model_Votes_Individual())->getDefaultAdapter();

        $form = new Default_Form_VotesConfirmation();

        if ($this->_request->isPost()) {
            if ($this->_consultation['vot_to'] === '0000-00-00 00:00:00'
                || Zend_Date::now()->isEarlier(new Zend_Date($this->_consultation['vot_to'], Zend_Date::ISO_8601))
            ) {
                $data = $this->_request->getPost();
                if (!empty($data['confirm'])) {
                    $dbAdapter->beginTransaction();
                    try {
                        $votingService->confirmVotes($confirmationHash);
                        $dbAdapter->commit();
                        $this->_flashMessenger->addMessage('Votes were confirmed.', 'success');
                    } catch (Dbjr_Voting_Exception $ex) {
                        $dbAdapter->rollBack();
                        $this->_flashMessenger->addMessage('Votes confirmation error.', 'error');
                    }
                } elseif (!empty($data['reject'])) {
                    $dbAdapter->beginTransaction();
                    try {
                        $votingService->rejectVotes($confirmationHash);
                        $dbAdapter->commit();
                        $this->_flashMessenger->addMessage('Votes were deleted.', 'success');
                    } catch (Dbjr_Voting_Exception $ex) {
                        $dbAdapter->rollBack();
                        $this->_flashMessenger->addMessage('Votes deleting error.', 'error');
                    }
                } else {
                    $this->_flashMessenger->addMessage('Invalid action invoked.', 'error');
                }
            } else {
                $this->_flashMessenger->addMessage('Voting period has ended and it is not possible to change voting results; the voting results are no longer subject to change.', 'error');
            }
        } else {
            $this->view->settings = $this->getVotingSettings();
            $votesData = (new Model_Votes_Uservotes())->fetchVotesToConfirm($confirmationHash);
            $this->view->votesData = $votesData;

            if ($this->_consultation['vot_to'] !== '0000-00-00 00:00:00'
                && Zend_Date::now()->isLater(new Zend_Date($this->_consultation['vot_to'], Zend_Date::ISO_8601))
            ) {
                $form->disable();
            }
            
            $this->view->form = $form;
            if (empty($votesData)) {
                $this->_flashMessenger->addMessage('No unconfirmed votes to process.', 'info');
            } else {
                return;
            }
        }

        $this->redirect('/input/index/kid/' . $this->_consultation['kid']);
    }

    /**
     * Groupleader confirms member of his group
     */
    public function confirmmemberAction()
    {
        $act = $this->getRequest()->getParam('act');
        $subuid = $this->getRequest()->getParam('user');
        $authcode = $this->getRequest()->getParam('authcode');

        if (empty($act) || empty($subuid) || empty($authcode)) {
            $this->_flashMessenger->addMessage('The link is not correct.', 'error');
            $this->redirect('/');
        }

        if ($this->_consultation['vot_to'] !== '0000-00-00 00:00:00'
            && Zend_Date::now()->isLater(new Zend_Date($this->_consultation['vot_to'], Zend_Date::ISO_8601))
        ) {
            $this->_flashMessenger->addMessage('Voting period has ended and it is not possible to change voting results; the voting results are no longer subject to change.', 'error');
            $this->redirect('/');
        }

        $votingRights = (new Model_Votes_Rights())->findByCode($authcode);

        if (!$votingRights) {
            $this->_flashMessenger->addMessage('The link is not correct.', 'error');
            $this->redirect('/');
        }

        $votingGroupModel = new Model_Votes_Groups();
        $votingGroup = $votingGroupModel->getByUser($votingRights['uid'], $subuid, $this->_consultation->kid);

        $confirmCode = md5($votingGroup['sub_user'] . $subuid . 'y');
        $rejectCode = md5($votingGroup['sub_user'] . $subuid . 'n');

        if ($act === $confirmCode) {
            $result = $votingGroupModel->confirmVoter($this->_consultation->kid, $votingRights['uid'], $subuid);
            if ($result) {
                $this->view->act = 'confirm';
            }
        } elseif ($act === $rejectCode) {
            $result = $votingGroupModel->denyVoter($this->_consultation->kid, $votingRights['uid'], $subuid);
            if ($result) {
                $this->view->act = 'reject';
            }
        } else {
            $this->_flashMessenger->addMessage('The link is not correct.', 'error');
            $this->redirect('/');
        }

        $this->view->subuser = $votingGroup['sub_user'];
    }

    public function resultsAction()
    {
        $qid = $this->_request->getParam('qid', 0);

        if ($this->_consultation['vot_res_show'] === 'n') {
            $this->_flashMessenger->addMessage('Voting results are not available yet.', 'error');
            $this->redirect('/voting/index/kid/' . $this->_consultation['kid']);
        }

        $articlesModel = new Model_Articles();
        $this->view->articleGeneral = $articlesModel->getByRefName('vot_res', 0);
        $this->view->articleConsultation = $articlesModel->getByRefName('vot_res', $this->_consultation['kid']);

        $votingResultsValues = (new Model_Votes())->getResultsValues($this->_consultation['kid'], $qid);
        $this->view->assign($votingResultsValues);
    }
}
