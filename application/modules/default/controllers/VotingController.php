<?php
/**
 * VotingController
 * @desc Abstimmung
 * @author Markus Hackel, Jan Suchandt
 */
class VotingController extends Zend_Controller_Action
{
    protected $_user = null;
    protected $_consultation = null;
    protected $_flashMessenger = null;

    /**
     * Construct
     * @see Zend_Controller_Action::init()
     * @return void
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
    * getVotingRightsSession()
    * @param session params
    * @return bool or redirect
    **/
    private function getVotingRightsSession()
    {
        $votingRightsSession = new Zend_Session_Namespace('votingRights');
        if ($votingRightsSession->access != $this->_consultation->kid) {
            $this->_flashMessenger->addMessage('In dieser Beteiligungsrunde kann derzeit nicht abgestimmt werden.', 'error');
            $this->redirect('/');
        }

        return $votingRightsSession;
    }

    /**
    * getVotingSettings()
    * @param kid
    * @return array
    **/
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
    **/
    private function getVotingBasket($subUid)
    {
           $votingBasket = array();
           $votingIndividualModel = new Model_Votes_Individual();

           $votingBasket["countvotes"] = $votingIndividualModel->countParticularImportantVote($subUid);
           $votingBasket["votes"] = $votingIndividualModel->getParticularImportantVote($subUid);

           return $votingBasket;
    }


    /**
    *    checkVotingDate()
    * @param $this->_consultation from init()
    * @return redirect
    **/
    public function checkVotingDate()
    {
        $date = new Zend_Date();
        $nowDate = Zend_Date::now();

        if ($nowDate->isEarlier($this->_consultation->vot_fr)) {
            $this->_flashMessenger->addMessage('Derzeit ist es nicht möglich an der Abstimmung teilzunehmen.', 'info');
            $this->redirect('/');
        } elseif ($nowDate->isLater($this->_consultation->vot_to) && $this->_consultation->vot_to != '0000-00-00 00:00:00' && $this->_consultation->vot_res_show == 'y') {
            $this->_flashMessenger->addMessage('Die Abstimmung ist beendet. Unten k&ouml;nnt ihr euch die Ergebnisse ansehen.', 'info');
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
            $this->redirect('/voting/overview/kid/' . $this->_consultation->kid);
        } elseif ($this->_request->isPost()) {  // request sended
            $data = $this->_request->getPost();
            // if form is valud
            if ($form->isValid($data)) {
                $emailAddress = $this->getRequest()->getParam('email');
                $authcode = $this->getRequest()->getParam('authcode');
                $votingRightModel = new Model_Votes_Rights();
                $votingRights = $votingRightModel->findByCode($authcode);
                // check if votingcode is correct
                if (!empty($votingRights)) {
                    if ($votingRights['kid'] == $this->_consultation->kid) {
                        $votingGroupModel = new Model_Votes_Groups();
                        $votingSubuser = $votingGroupModel->getByEmail(
                            $emailAddress,
                            $votingRights['uid'],
                            $this->_consultation->kid
                        );
                        // no subuser => no groupmember
                        if (empty($votingSubuser)) {
                            // create list of all votable inputs
                            $inputModel = new Model_Inputs();
                            // $votingInputChain = $inputModel->getVotingchain($this->_consultation->kid);
                            // $votinglist = implode(',', $votingInputChain['tid']);
                            // $questionList = implode(',', $votingInputChain['qi']);
                            // subuid
                            $subUid = md5($emailAddress . $this->_consultation->kid);
                            // save subuser
                            $data = array(
                                'uid'=>$votingRights['uid'],
                                'sub_user'=>$emailAddress,
                                'sub_uid'=>$subUid,
                                'kid'=>$this->_consultation->kid,
                                'member'=>'u'
                                // 'vt_inp_list'=>$votinglist,
                                // 'vt_rel_qid'=>$questionList
                            );
                            if (!$votingGroupModel->add($data)) {
                                throw new Exception('Fehler im Abstimmung. Bitte kontaktieren Sie den Administrator.');
                            }
                        } else {  // we got a subuser
                            // check if subuser is blocked
                            if ($votingSubuser['member'] == 'n') {
                                // @todo user is blocked, but we dont know what to do (old system is not working)
                            } else {
                                // @todo user is unconfirmed, but we dont know what to do
                            }
                            $subUid = $votingSubuser['sub_uid'];
                            #$votingInputChain = $votingSubuser['vt_inp_list'];
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
                        $this->redirect('/voting/overview/kid/' . $this->_consultation->kid);
                    } else {
                        $this->_flashMessenger->addMessage('Ihre Eingaben sind nicht korrekt. Bitte pr&uuml;fen Sie diese.', 'error');
                        $form->populate($data);
                    }
                } else { // no access for voting
                    $this->_flashMessenger->addMessage('Ihre Eingaben sind nicht korrekt. Bitte pr&uuml;fen Sie diese.', 'error');
                    $form->populate($data);
                }
            } else { // invalid form
                $this->_flashMessenger->addMessage('Ihre Eingaben sind nicht korrekt. Bitte pr&uuml;fen Sie diese.', 'error');
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

        $uid = $votingRightsSession->uid;
        $subUid = $votingRightsSession->subUid;
        $kid = $this->_consultation->kid;

        // Questions
        $questionModel = new Model_Questions();
        $questions = $questionModel->getByConsultation($kid)->toArray();
        $questionResult = array();

        // inputs per question with uservoting
        $votingUserInputModel = new Model_Votes_Uservotes();
        $votingUserInput = array();

        $i = 0;
        foreach ($questions as $question) {
            $questionID = $question['qi'];
            $questionResult["$i"] = $question;
            $questionResult["$i"]['QuestionsAndInputs'] = $votingUserInputModel->fetchAllInputsWithUserVotes($questionID, $subUid, $kid);
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

        $votingIndividualModel = new Model_Votes_Individual();

        $param = $this->getRequest()->getParams();
        $pts = (int) $param['points'];

        if ($pts < 0 || $pts > 5) {
            $this->view->error = "1";
            $this->view->error_comment = "Die Anzahl der vergebenen Punkte ist nicht korrekt";

            return;
        }

        $subUid = $votingRightsSession->subUid;
        $uid = (int) $votingRightsSession->uid;
        $kid = $this->_consultation->kid;

        $votingSuccess = $votingIndividualModel->updateVote($param['id'], $subUid, $uid, $pts);

        if (!$votingSuccess) {
            $this->view->error = "1";
            $this->view->error_comment = "Es ist ein Fehler aufgetreten";
            return;
        } else {
            $feedback = array('points' => $votingSuccess['points'],'pimp' => $votingSuccess['pimp'], 'tid' => $param['id']);
            $this->view->feedback = $feedback;
        }

    }

    /**
     * ajaxresponse from previewAction by click the particular important button
     * @author Karsten Tackmann
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
            $this->view->error_comment = "Die Auswahl des Superbuttons ist nicht erlaubt";

            return;
        }
        // count max possibility click on particularly important button
        // returns comment for user or action and return buttons
        $kid = $this->_consultation->kid;
        $param = $this->getRequest()->getParams();
        $votingIndividualModel = new Model_Votes_Individual();
        $votingSuccess = $votingIndividualModel->updateParticularImportantVote(
            $param['id'],
            $votingRightsSession->subUid,
            (int) $votingRightsSession->uid,
            $this->view->settings['btn_numbers'],
            $this->view->settings['btn_important_factor'],
            $this->view->settings['btn_important_max']
        );
        if (isset ($votingSuccess['points'])) {
            $feedback = array('points' => $votingSuccess['points'], 'tid' => $param['id'], 'pimp' => $votingSuccess['pimp']);
        } elseif (isset($votingSuccess['max'])) {
            $this->view->error = "1";
            $this->view->error_comment = "Du hast schon zu oft diesen Button benutzt, bitte ändere zunächst andere Votings. Diese Abstimmung wurde nicht gezählt!";
            $currentVote = $votingIndividualModel->getCurrentVote($param['id'], $votingRightsSession->subUid);
            $feedback = array('points' => $currentVote['pts'], 'tid' => $param['id'], 'pimp' => $currentVote['pimp']);
        } else {
            $this->view->error = "1";
            $feedback = array();
        }

        $this->view->feedback = $feedback;
    }

    /**
     * ajaxresponse for delete Supervote from basket
     * @author Karsten Tackmann
     */
    public function removethesisAction()
    {
        #if(!$this->getRequest()->isXmlHttpRequest()) exit; //no AjaxRequest
         $this->_helper->layout()->disableLayout();

        $votingRightsSession = $this->getVotingRightsSession();
        $subUid = $votingRightsSession->subUid;
        $uid = (int) $votingRightsSession->uid;
        $param = $this->getRequest()->getParams();
        $tid= (int) $param['tid'];

        $votingUserInputModel = new Model_Votes_Individual();


        if ($votingUserInputModel->deleteParticularImportantVote($uid, $subUid, $tid)) {
            $this->view->response = "success";
        } else {
            $this->view->response = "error";
        }
    }

    // Trennt die Thesen nach gevoted oder nicht
    public function filterStatements($questionResult)
    {
        $questionResultVoted = array();
        $questionResultUnVoted = array();

        foreach ($questionResult as $key => $value) {
            (!empty($value["status"])) ? ($questionResultVoted[$value['tid']] = $value) :    $questionResultUnVoted[$value['tid']] = $value ; //use for quick fix the backbutton and votingcount!!
        }

        $questionResultSeparated =array("questionResultUnVoted" => $questionResultUnVoted, "questionResultVoted" => $questionResultVoted);

        return $questionResultSeparated;
    }

    /**
     * Voting filtered by tags or questions
     * note: need authentification over session
     */
    public function voteAction()
    {
        // no access, redirect back
        $votingRightsSession = $this->getVotingRightsSession();

        $kid = (int) $this->_consultation->kid;
        $qid = (int) $this->getRequest()->getParam('qid');
        $tagId = (int) $this->getRequest()->getParam('tag');
        $tid =(int) $this->getRequest()->getParam('tid');
        $subUid = $votingRightsSession->subUid;
        $uid = (int) $votingRightsSession->uid;  //use for quick fix the backbutton !!

        //use for fix the backbutton begin !!
        if (!empty($tid)) {
            $votIndiModel = new Model_Votes_Individual();
            $votIndiModel->deleteParticularImportantVote($uid, $subUid, $tid);
        }
        //use for fix the bachbutton end !!

        if (empty($qid) && empty($tagId)) {
            $this->redirect('/voting/overview/kid/'.$kid);
        }
        $votingUserInputModel = new Model_Votes_Uservotes();
        $votingUserInput = array();
        // all statements/theses from Question // oder zum Tag
        if (!empty($qid)) {
            $questionResult = $votingUserInputModel->fetchAllInputsWithUserVotes($qid, $subUid, $kid);
        } else {
            $questionResult = $votingUserInputModel->fetchAllInputsWithUserVotes(null, $subUid, $kid, $tagId);
        }


        // votes inputs and unvotetd inputs
        $questionResultSeparated = $this->filterStatements($questionResult);

        $thesesCount = count($questionResult);
        $thesesVoted =$questionResultSeparated["questionResultVoted"];

        $thesesVotedCount = count($thesesVoted);
        $thesesUnVoted =$questionResultSeparated["questionResultUnVoted"];
        $thesesUnVotedCount = count($thesesUnVoted);

        $questionModel = new Model_Questions();
        if ($thesesUnVotedCount == 0) {
                $this->view->noMoreThesis = true;
        } else {
            //use for quick fix the backbutton begin !!
            if (empty($tid)) {
                $rand_keys = array_rand($thesesUnVoted, 1);
                // get thesis
                $thesis= $thesesUnVoted[$rand_keys];
            } else { // backbutton is in use
                $thesis= $thesesUnVoted[$tid];
            }
            //use for quick fix the backbutton end !!

            // get question
            $question = $questionModel->getById($thesis['qi']);
            $this->view->thesis = $thesis;
            $this->view->question = $question;
        }

        // Params for View
        // theses total
        $this->view->thesesCount = $thesesCount;
        $this->view->thesesCountVoted = $thesesVotedCount;
        $this->view->settings = $this ->getVotingSettings();

        $this->view->votingBasket= $this ->getVotingBasket($subUid);

        $votingIndividualModel = new Model_Votes_Individual();
        // Check last voted thesis and append to view
        $lastTid = $votingIndividualModel->getLastBySubuser($subUid);
        if (!empty($lastTid)) {
            $this->view->LastVote = $lastTid;
        }
    }

    public function thesisvoteAction()
    {
        // no access, redirect back
        $votingRightsSession = $this->getVotingRightsSession();
        // no view and layout
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $param = $this->getRequest()->getParams();
        $backParam = (!empty($param['qid'])) ? '/qid/' . $param['qid'] : '/tag/' . $param['tag'];
        $pts = (int) $param['pts'];
        $subUid = $votingRightsSession->subUid;
        $uid = $votingRightsSession->uid;

        // check if a tid is given
        if (empty($param['tid']) || (empty($param['qid']) && empty($param['tag']))) {
            $this->_flashMessenger->addMessage('Etwas ist schief gelaufen.', 'info');
            $this->redirect('/voting/overview/kid/'.$this->_consultation->kid);
        }

        // check if the points are correct
        if ($pts >5 && $pts   < 0) {
            $this->_flashMessenger->addMessage('Die vergebenen Punkte sind nicht korrekt.', 'info');
            $this->redirect('/voting/vote/kid/' . $this->_consultation->kid);
        }

        $votIndiModel = new Model_Votes_Individual();
        $votingSuccess = $votIndiModel->updateVote($param['tid'], $subUid, $uid, $pts);
        if ($votingSuccess) {

            $this->_flashMessenger->addMessage('Deine Abstimmung wurde gezählt!', 'info');
             $this->redirect('/voting/vote/kid/' . $this->_consultation->kid . $backParam);

        } else {
            $this->_flashMessenger->addMessage('Deine Abstimmung konnte nicht eingetragen werden. (1)', 'info');
            $this->redirect('/voting/vote/kid/' . $this->_consultation->kid . '/tid/' . $param['tid'] . $backParam);
        }
    }

    public function thesissupervoteAction()
    {
        // no access, redirect back
        $votingRightsSession = $this->getVotingRightsSession();
        // no view and layout
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->settings = $this->getVotingSettings();

        $param = $this->getRequest()->getParams();
        $backParam = (!empty($param['qid'])) ? '/qid/' . $param['qid'] : '/tag/' . $param['tag'];
        $pts = (string) $param['pts'];
        $subUid = $votingRightsSession->subUid;
        $uid = $votingRightsSession->uid;

        if ($this->settings['btn_important'] == 'n') {
            $this->flashMessenger -> addMessage('Die Auswahl des Superbuttons ist nicht erlaubt.', 'info');
            $this->redirect('/voting/vote/kid/' . $this->_consultation->kid);
            return;
        }

        // check if the points are correct
        if ($pts != 'y') {
            $this->_flashMessenger -> addMessage('Die vergebenen Punkte sind nicht korrekt.', 'info');
            $this->redirect('/voting/vote/kid/' . $this->_consultation->kid);
        }

        // check if a tid is given
        if (empty($param['tid']) || (empty($param['qid']) && empty($param['tag']))) {
            $this->_flashMessenger->addMessage('Bitte wähle ein Frage oder ein Schlagwort aus.', 'info');
            $this->redirect('/voting/overview');
        }

        $votingIndividualModel = new Model_Votes_Individual();
        $votingSuccess = $votingIndividualModel->updateParticularImportantVote(
            $param['tid'],
            $votingRightsSession -> subUid,
            (int) $votingRightsSession -> uid,
            $this -> settings['btn_numbers'],
            $this -> settings['btn_important_factor'],
            $this -> settings['btn_important_max']
        );

        if (!$votingSuccess) {
            $this->_flashMessenger->addMessage('Deine Abstimmung konnte nicht eingetragen werden. (1)', 'info');
            $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid . '/tid/' . $param['tid'] . $backParam);

        } elseif (!isset($votingSuccess['max'])) {

                $this -> _flashMessenger -> addMessage('Deine Abstimmung wurde gezählt.', 'info');
                $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid . $backParam);

        }
        if (isset($votingSuccess['max'])) {
                $this -> _flashMessenger -> addMessage('Du hast schon zu oft diesen Button benutzt, bitte ändere zunächst folgende Votings. Diese Abstimmung wurde nicht gezählt!', 'info');
                $this -> redirect('/voting/preview/kid/' . $this -> _consultation -> kid. $backParam);
        }
    }

    /**
     * User stop the voting, if user member of group, send confirm-email to this user
     */
    public function stopvotingAction()
    {
        $votingRightsSession = new Zend_Session_Namespace('votingRights');
        $userModel = new Model_Users();
        // Send mails to owner of group
        $uid = $votingRightsSession->uid;
        $subUid = $votingRightsSession->subUid;
        $user = $userModel->getById($uid);
        // user is member of group, send mail for his confirmation
        if ($votingRightsSession->weight > 1 || $votingRightsSession->weight == 0) {
            $votingGroup = new Model_Votes_Groups();
            $subuser = $votingGroup->getByUser($uid, $subUid);

            // user deleted by admin or groupadmin after login for voting //
            if (empty( $subuser)) {
                $votingRightsSession->unsetAll();
                $this->_flashMessenger->addMessage('User konnte nicht gefunden werden.', 'error');
                $this->redirect('/voting/preview/kid/' . $this->_consultation->kid. $backParam);
            }

            $actionUrl = Zend_Registry::get('baseUrl') . '/voting/confirmvoting/kid/' . $this->_consultation->kid .
                '/authcode/' . $votingRightsSession->vtc . '/user/' . $subUid;

            $mailer = new Dbjr_Mail();
            $mailer
                ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_CONFIRMATION_SINGLE)
                ->setPlaceholders(
                    array(
                        'to_email' => $subuser['sub_user'],
                        'confirmation_url' => $actionUrl . '/act/acc/',
                        'rejection_url' => $actionUrl . '/act/rej/',
                        'consultation_title_short' => $this->_consultation->titl_short,
                        'consultation_title_long' => $this->_consultation->titl,
                    )
                )
                ->addTo($subuser['sub_user'])
                ->send();

            $this->view->groupmember = $subuser['sub_user'];
        } else { // if singleuser (no group) just update status of his votes
            $voteIndiviModel = new Model_Votes_Individual();
            $result = $voteIndiviModel->setStatusForSubuser($uid, $subUid, 'c');
        }
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
        $act = $this->getRequest()->getParam('act');
        $subuid = $this->getRequest()->getParam('user');
        $authcode = $this->getRequest()->getParam('authcode');

        // action or subuid is not given
        if (($act != 'acc' && $act != 'rej') || empty($subuid) || empty($authcode)) {
            $this->_flashMessenger->addMessage('Der angew&auml;hlte Link aus ist nicht korrekt.', 'error');
            $this->redirect('/');
        }

        // get rights by authcode
        $votingRightModel = new Model_Votes_Rights();
        $votingRights = $votingRightModel->findByCode($authcode);

        // No access
        if (!$votingRights) {
            $this->_flashMessenger->addMessage('Der angew&auml;hlte Link aus ist nicht korrekt.', 'error');
            $this->redirect('/');
        }

        // get group
        $votingGroupModel = new Model_Votes_Groups();
        $votingGroup = $votingGroupModel->getByUser($votingRights['uid'], $subuid);

        // confirm
        $votingIndivModel = new Model_Votes_Individual();
        if ($act == 'acc') {
            // If user is singleuser (not group)
            if ($votingRights['vt_weight'] > 1 || $votingRights['vt_weight'] == 1) {
                $result = $votingIndivModel->setStatusForSubuser($votingRights['uid'], $subuid, 'v', 'c');
            }
            $this->view->heading = 'Deine Bewertungen sind jetzt bestätigt.';
        } elseif ($act == 'rej') { // reject votes
            // If user is singleuser (not group)
            if ($votingRights['vt_weight'] > 1 || $votingRights['vt_weight'] == 1) {
                $result = $votingIndivModel->deleteByStatusForSubuser($votingRights['uid'], $subuid, 'v');
            }
        }

        // send mail to singleuser/groupleader if user in unconfirmed
        if ($votingGroup['member'] == 'u') {
            // get groupleader
            $userModel = new Model_Users();
            $leader = $userModel->getById($votingGroup['uid']);
            $actionUrl = Zend_Registry::get('baseUrl') . '/voting/confirmmember/kid/' .  $this->_consultation->kid
                . '/authcode/' . $authcode . '/user/' . $subuid;

            $mailer = new Dbjr_Mail();
            $mailer
                ->setTemplate(Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_CONFIRMATION_GROUP)
                ->setPlaceholders(
                    array(
                        'to_name' => $leader['name'] ? $leader['name'] : $leader['email'],
                        'to_email' => $leader['email'],
                        'voter_email' => $votingGroup['sub_user'],
                        'confirmation_url' => $actionUrl . '/act/' . md5($votingGroup['sub_user'] . $subuid . 'y'),
                        'rejection_url' => $actionUrl . '/act/' . md5($votingGroup['sub_user'] . $subuid . 'n'),
                        'consultation_title_short' => $this->_consultation->titl_short,
                        'consultation_title_long' => $this->_consultation->titl,
                    )
                )
                ->addTo($leader['email'])
                ->send();
        }

        $this->view->act = $act;
        $this->view->memberstatus = $votingGroup['member'];
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
            $this->_flashMessenger->addMessage('Der angew&auml;hlte Link aus ist nicht korrekt.', 'error');
            $this->redirect('/');
        }

        $votingRightModel = new Model_Votes_Rights();
        $votingRights = $votingRightModel->findByCode($authcode);

        if (!$votingRights) {
            $this->_flashMessenger->addMessage('Der angew&auml;hlte Link aus ist nicht korrekt.', 'error');
            $this->redirect('/');
        }

        // get group
        $votingGroupModel = new Model_Votes_Groups();
        $votingGroup = $votingGroupModel->getByUser($votingRights['uid'], $subuid);

        $confirmCode = md5($votingGroup['sub_user'] . $subuid . 'y');
        $rejectCode = md5($votingGroup['sub_user'] . $subuid . 'n');

        // confirm
        if ($act == $confirmCode) {
            // set status of sub_user to 'y'
            $result = $votingGroupModel->confirmVoter($this->_consultation->kid, $votingRights['uid'], $subuid);
            if ($result) {
                $this->view->act = 'confirm';
            }
        } elseif ($act == $rejectCode) {
            // set status of sub_user to 'n'
            $result = $votingGroupModel->denyVoter($this->_consultation->kid, $votingRights['uid'], $subuid);
            if ($result) {
                $this->view->act = 'reject';
            }
        } else {
            $this->_flashMessenger->addMessage('Der angew&auml;hlte Link aus ist nicht korrekt.', 'error');
            $this->redirect('/');
        }

        $this->view->subuser = $votingGroup['sub_user'];

    }

    public function resultsAction()
    {
        if ($this->_consultation->vot_res_show == 'n') {
            $this->_flashMessenger->addMessage('Abstimmungsergebnisse können noch nicht gezeigt werden!', 'error');
            $this->redirect('/voting/index/kid/' . $this->_consultation->kid);
        }

        $articlesModel = new Model_Articles();
        $this->view->articleGeneral = $articlesModel->getByRefName('vot_res', 0);
        $this->view->articleConsultation = $articlesModel->getByRefName('vot_res', $this->_consultation->kid);

        $qid = $this->_request->getParam('qid', 0);
        $votesModel = new Model_Votes();
        $votingResultsValues = $votesModel->getResultsValues($this->_consultation->kid, $qid);
        $this->view->assign($votingResultsValues);
    }
}
