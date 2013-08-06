<?php
/**
 * VotingController
 * @desc Abstimmung
 * @author Markus Hackel, Jan Suchandt
 */
class VotingController extends Zend_Controller_Action {

  protected $_user = null;
  protected $_consultation = null;
  protected $_flashMessenger = null;

  /**
   * Construct
   * @see Zend_Controller_Action::init()
   * @return void
   */
  public function init() {
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
   *  get Voting Right of User
   */

  private function getVotingRightsSession () {

    $votingRightsSession = new Zend_Session_Namespace('votingRights');
    if ($votingRightsSession -> access != $this -> _consultation -> kid) {
      $this -> _flashMessenger -> addMessage('In dieser Konsultation kann derzeit nicht abgestimmt werden.', 'error');
      $this -> redirect('/');
    }
    return $votingRightsSession;
  }

  /**
   * returns the setting for this voting (number of buttons, labels for buttons etc.)
   */
  private function getVotingSettings() {

      $kid = $this -> _consultation -> kid;
      $settingsModel = new Model_Votes_Settings();
      $votingSettings = $settingsModel -> getById($kid);
      return $votingSettings;
  }


  function checkVotingDate () {

    $date = new Zend_Date();
    $nowDate = Zend_Date::now();

    if($nowDate->isEarlier($this->_consultation->vot_fr)) {
      $this->_flashMessenger->addMessage('Derzeit ist es nicht möglich an der Abstimmung teilzunehmen.', 'info');
      $this->redirect('/');
    } elseif($nowDate->isLater($this->_consultation->vot_to) && $this->_consultation->vot_to != '0000-00-00 00:00:00' && $this->_consultation->vot_res_show == 'y') {
      $this->_flashMessenger->addMessage('Die Abstimmung ist beendet. Unten k&ouml;nnt ihr euch die Ergebnisse ansehen.', 'info');
      $this->redirect('/voting/results/kid/'.$this->_consultation->kid);
    }
  }

  /**
   * form for access for voting, check email and authcode
   */
  public function indexAction() {
    $votingRightsSession = new Zend_Session_Namespace('votingRights');
    $form = new Default_Form_Voting_Authentification();
    // check if voting is in time
    $this -> checkVotingDate();
    // if session is allready created, forword to overview
    if ($votingRightsSession -> access == $this -> _consultation -> kid) {
      $this->redirect('/voting/overview/kid/'.$this->_consultation->kid);
    }
    // request sended
    elseif($this->_request->isPost()) {
      $data = $this->_request->getPost();
      // if form is valud
      if($form->isValid($data)) {
        $emailAddress = $this->getRequest()->getParam('email');
        $authcode = $this->getRequest()->getParam('authcode');
        $votingRightModel = new Model_Votes_Rights();
        $votingRights = $votingRightModel->findByCode($authcode);
        // check if votingcode is correct
        if(!empty($votingRights)) {
          if($votingRights['kid'] == $this->_consultation->kid) {
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
              $votingInputChain = $inputModel->getVotingchain($this->_consultation->kid);
              $votinglist = implode(',', $votingInputChain['tid']);
              $questionList = implode(',', $votingInputChain['qi']);
              // subuid
              $subUid = md5($emailAddress.$this->_consultation->kid);
              // save subuser
              $data = array(
                'uid'=>$votingRights['uid'],
                'sub_user'=>$emailAddress,
                'sub_uid'=>$subUid,
                'kid'=>$this->_consultation->kid,
                'member'=>'u',
                'vt_inp_list'=>$votinglist,
                'vt_rel_qid'=>$questionList
              );
              if(!$votingGroupModel->add($data)) {
                throw new Exception('Fehler bei der Abstimmung. Bitte kontaktiere den Administrator.');
              }
            }
            // we got a subuser
            else {
              // check if subuser is blocked
              if ($votingSubuser['member'] == 'n') {
                // @todo user is blocked, but we dont know what to do (old system is not working)
              }
              else {
                // @todo user is unconfirmed, but we dont know what to do
              }
              $subUid = $votingSubuser['sub_uid'];
              $votingInputChain = $votingSubuser['vt_inp_list'];
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
            $this->redirect('/voting/overview/kid/'.$this->_consultation->kid);
          } else {
            $this->_flashMessenger->addMessage('Die Eingaben sind nicht korrekt. Bitte überprüfe diese noch einmal.', 'error');
            $form->populate($data);
          }
        }
        // no access for voting
        else {
          $this->_flashMessenger->addMessage('Die Eingaben sind nicht korrekt. Bitte überprüfe diese noch einmal.', 'error');
          $form->populate($data);
        }
      }
      // invalid form
      else {
        $this->_flashMessenger->addMessage('Die Eingaben sind nicht korrekt. Bitte überprüfe diese noch einmal.', 'error');
        $form->populate($data);
      }
    }
    // Check if user comes from email with authcode
    else {
      $authcode = $this->getRequest()->getParam('authcode');
      if(!empty($authcode)) {
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
  public function overviewAction() {
    // no access, redirect back
    $votingRightsSession = new Zend_Session_Namespace('votingRights');
//    $votingRightsSession->unsetAll();
    if($votingRightsSession->access != $this->_consultation->kid) {
      $this->redirect('/');
    }
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
  public function previewAction() {
    // no access, redirect back
    $votingRightsSession = $this -> getVotingRightsSession ();

    $this -> view -> settings = $this  -> getVotingSettings();

    $uid = $votingRightsSession -> uid;
    $subUid = $votingRightsSession -> subUid;
    $kid = $this -> _consultation -> kid;

    // Questions
    $questionModel = new Model_Questions();
    $questions = $questionModel -> getByConsultation($kid) -> toArray();
    $questionResult = array();

    // inputs per question with uservoting
    $votingUserInputModel = new Model_Votes_Uservotes();
    $votingUserInput = array();

    $i = 0;
    foreach ($questions as $question) {
      $questionID = $question['qi'];
      $questionResult["$i"] = $question;
      $questionResult["$i"]['QuestionsAndInputs'] = $votingUserInputModel -> fetchAllInputsWithUserVotes($questionID, $subUid, $kid);
      $i++;
    }

    $this -> view -> questions = $questionResult;

    // count of votable inputs
    $filter = array( array('field' => 'vot', 'operator' => '=', 'value' => 'y'));

    $inputModel = new Model_Inputs();
    $this -> view -> votableInputs = $inputModel -> getCountByConsultationFiltered($kid, $filter);
    // count of voted inputs
    $votingIndivModel = new Model_Votes_Individual();
    $this -> view -> votedInputs = $votingIndivModel -> countVotesBySubuser($votingRightsSession -> subUid);

  }


  /**
   * ajaxresponse from previewAction
   * note: need authentification over session
   * @author Karsten Tackmann
   */
  public function previewfeedbackAction() {
    $this -> _helper -> layout() -> disableLayout();
    $votingRightsSession = $this -> getVotingRightsSession ();

    $this -> view -> settings = $this -> getVotingSettings();

    $votingIndividualModel = new Model_Votes_Individual();

    $param = $this -> getRequest() -> getParams();
    $pts = (int)$param['points'];

    if ($pts < 0 || $pts > 5) {
      $this -> view -> error = "1";
      $this -> view -> error_comment = "Die Anzahl der  vergebenen Punkte ist nicht korrekt";
      return;
    }

    $subUid = $votingRightsSession->subUid;
    $uid = (int)$votingRightsSession -> uid;
    $kid = $this -> _consultation -> kid;

    $votingSuccess = $votingIndividualModel -> updateVote($param['id'], $subUid, $uid, $pts);


    if (!$votingSuccess) {
      $this -> view -> error = "1";
      $this -> view -> error_comment = "Es ist ein Fehler aufgetreten";
      return;
    } else {
      $feedback = array('points' => $votingSuccess['points'],'pimp' => $votingSuccess['pimp'], 'tid' => $param['id']);
      $this -> view -> feedback = $feedback;
    }


  }

  /**
   * ajaxresponse from previewAction by click the particular important button
   * @author Karsten Tackmann
   */ public function previewfeedbackpiAction() {

      $this -> _helper -> layout() -> disableLayout();
      $votingRightsSession = $this -> getVotingRightsSession ();

      $this -> view -> settings = $this -> getVotingSettings();

      if ( $this -> view -> settings['btn_important'] == 'n' ) {
        $this -> view -> error = "1";
        $this -> view -> error_comment = "Die Auswahl des Superbuttons ist nicht erlaubt";
        return;
      }
       // count max possibility click on particularly important button
       // returns comment for user or action and return buttons
      $kid = $this -> _consultation -> kid;
      $param = $this -> getRequest() -> getParams();

    $votingIndividualModel = new Model_Votes_Individual();

       $votingSuccess = $votingIndividualModel
                     -> updateParticularImportantVote (
                         $param['id'],
                         $votingRightsSession -> subUid,
                         (int)$votingRightsSession -> uid,
                         $this -> view -> settings['btn_numbers'],
                         $this -> view -> settings['btn_important_factor'],
                         $this -> view -> settings['btn_important_max']
                    );

      if (isset ($votingSuccess['points'])) {

          $feedback = array('points' => $votingSuccess['points'], 'tid' => $param['id'], 'pimp' => $votingSuccess['pimp']);

       } elseif (isset($votingSuccess['max'])) {

          $this -> view -> error = "1";
          $this -> view -> error_comment = "Du hast schon zu oft diesen Button benutzt, bitte ändere zunächst andere Votings. Diese Abstimmung wurde nicht gezählt!";
          $currentVote = $votingIndividualModel -> getCurrentVote($param['id'], $votingRightsSession -> subUid);
          $feedback = array('points' => $currentVote['pts'], 'tid' => $param['id'], 'pimp' => $currentVote['pimp']);

      } else {

          $this -> view -> error = "1";
          $feedback = array();


      }

      $this -> view -> feedback = $feedback;

  }


  // Trennt die Votes nach gevoted oder nicht

  function filterStatements($questionResult) {

    $questionResultVoted = array();
    $questionResultUnVoted = array();

    foreach ($questionResult as $key => $value) {
      (!empty($value["points"])) ? ($questionResultVoted["$key"] = $value) :  $questionResultUnVoted["$key"] = $value ;
    }

    $questionResultSeparated =array("questionResultUnVoted" => $questionResultUnVoted, "questionResultVoted" => $questionResultVoted);
    return $questionResultSeparated;
  }



  /**
   * Voting filtered by tags or questions
   * note: need authentification over session
   */
  public function voteAction() {

    // no access, redirect back
    $votingRightsSession = $this -> getVotingRightsSession ();

    $kid = (int)$this -> _consultation -> kid;
    $qid = (int)$this -> getRequest() -> getParam('qid');
    $tagId = (int)$this -> getRequest() -> getParam('tag');
    $tid =(int) $this -> getRequest() -> getParam('tid');
    $subUid = $votingRightsSession -> subUid;

    if (empty($qid) && empty($tagId)) {
      $this -> redirect('/voting/overview/kid/'.$kid);
    }
    $votingUserInputModel = new Model_Votes_Uservotes();
    $votingUserInput = array();
    // all statements/theses from Question // oder zum Tag
    $questionResult = (!empty($qid)) ?  $votingUserInputModel -> fetchAllInputsWithUserVotes($qid, $subUid, $kid) :  $votingUserInputModel -> fetchAllInputsWithUserVotes(null,$subUid, $kid, $tagId);

    $questionResultSeparated = $this -> filterStatements($questionResult);

    $thesesCount = count($questionResult);
    $thesesVoted =$questionResultSeparated["questionResultVoted"];
    $thesesVotedCount = count($thesesVoted);
    $thesesUnVoted =$questionResultSeparated["questionResultUnVoted"];
    $thesesUnVotedCount = count($thesesUnVoted);


    $questionModel = new Model_Questions();
    if ($thesesUnVotedCount == 0)  {
        $this -> view -> noMoreThesis = true;
    } else {
      $rand_keys = array_rand($thesesUnVoted, 1);
      // get thesis
      $thesis= $thesesUnVoted[$rand_keys];
      // get question
      $question = $questionModel->getById($thesis['qi']);
      $this -> view -> thesis = $thesis;
      $this->view->question = $question;
    }

    // Params for View
    // theses total
    $this -> view -> thesesCount = $thesesCount;
    $this -> view -> thesesCountVoted = $thesesVotedCount;
    $this -> view -> settings = $this  -> getVotingSettings();

    $votingIndividualModel = new Model_Votes_Individual();
    // Check last voted thesis and append to view
    $lastTid = $votingIndividualModel->getLastBySubuser($subUid);
    if(!empty($lastTid)) {
      $this->view->LastVote = $lastTid;
    }

  }

  public function thesisvoteAction() {
    // no access, redirect back
    $votingRightsSession = new Zend_Session_Namespace('votingRights');
    if($votingRightsSession->access != $this->_consultation->kid) {
      $this->_flashMessenger->addMessage('Zu dieser Beteiligungsrunde kann derzeit nicht abgestimmt werden.', 'error');
      $this->redirect('/');
    }
    // no view and layout
    $this->_helper->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $param = $this->getRequest()->getParams();
    $backParam = (!empty($param['qid'])) ? '/qid/'.$param['qid'] : '/tag/'.$param['tag'];
    $pts = (int)$param['pts'];
    $subUid = $votingRightsSession->subUid;
    $uid = $votingRightsSession->uid;

    // check if a tid is given
    if(empty($param['tid']) || (empty($param['qid']) && empty($param['tag']))) {
      $this->_flashMessenger->addMessage('Etwas ist schief gelaufen.', 'info');
      $this->redirect('/voting/overview');
    }

    // check if the points are correct
    if ($pts >5 && $pts  <0) {
      $this->_flashMessenger->addMessage('Die vergebenen Punkte sind nicht korrekt', 'info');
      $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid );
    }

    $votIndiModel = new Model_Votes_Individual();
    $votingSuccess = $votIndiModel->updateVote($param['tid'], $subUid, $uid, $pts);
    if($votingSuccess) {

        // To do auslagern
        #$this -> votingChainSuccess($this -> _consultation -> kid, $subUid, $param['tid'], $backParam);
      $votGroupModel = new Model_Votes_Groups();
        $votingChainSuccess = $votGroupModel -> excludeThesisFromVotingchain($this -> _consultation -> kid, $subUid, $param['tid'], $backParam);
        if ($votingChainSuccess) {
            #$this -> _flashMessenger -> addMessage('Eingetragen', 'info');
            $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid . $backParam);
        } else {
        $this->_flashMessenger->addMessage('Abstimmung konnte nicht eingetragen werden (0)', 'info');
          $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid . $backParam);
        }
    } else {
      $this -> _flashMessenger -> addMessage('Abstimmung konnte nicht eingetragen werden (1)', 'info');
      $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid . '/tid/' . $param['tid'] . $backParam);
    }

  }

public function thesissupervoteAction() {
    // no access, redirect back
    $votingRightsSession = $this -> getVotingRightsSession ();
    // no view and layout
    $this -> _helper -> layout() -> disableLayout();
    $this -> _helper -> viewRenderer -> setNoRender(true);
    $this -> settings = $this -> getVotingSettings();

    $param = $this -> getRequest() -> getParams();
    $backParam = (!empty($param['qid'])) ? '/qid/' . $param['qid'] : '/tag/' . $param['tag'];
    $pts = (string)$param['pts'];
    $subUid = $votingRightsSession -> subUid;
    $uid = $votingRightsSession -> uid;

    if ( $this -> settings['btn_important'] == 'n' ) {
        $this -> _flashMessenger -> addMessage('Die Auswahl des Superbuttons ist nicht erlaubt', 'info');
        $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid );
        return;
    }

    // check if the points are correct
    if ($pts != 'y') {
      $this -> _flashMessenger -> addMessage('Die vergebenen Punkte sind nicht korrekt', 'info');
      $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid );
    }

    // check if a tid is given
    if (empty($param['tid']) || (empty($param['qid']) && empty($param['tag']))) {
      $this -> _flashMessenger -> addMessage('Bitte wähle ein Frage oder ein Schlagwort aus.', 'info');
      $this -> redirect('/voting/overview');
    }

    $votingIndividualModel = new Model_Votes_Individual();
     $votingSuccess = $votingIndividualModel
                     -> updateParticularImportantVote (
                         $param['tid'],
                         $votingRightsSession -> subUid,
                         (int)$votingRightsSession -> uid,
                         $this -> settings['btn_numbers'],
                         $this -> settings['btn_important_factor'],
                         $this -> settings['btn_important_max']
                    );

    if (!$votingSuccess) {
      $this->_flashMessenger->addMessage('Abstimmung konnte nicht eingetragen werden (1)', 'info');
      $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid . '/tid/' . $param['tid'] . $backParam);

    } elseif (!isset($votingSuccess['max'])) {

        //Todo auslagern wenns gebraucht wird
        #$this -> votingChainSuccess($this -> _consultation -> kid, $subUid, $param['tid'], $backParam);
        $votGroupModel = new Model_Votes_Groups();
        $votingChainSuccess = $votGroupModel -> excludeThesisFromVotingchain($this -> _consultation -> kid, $subUid, $param['tid'], $backParam);
        if ($votingChainSuccess) {
            $this -> _flashMessenger -> addMessage('Eingetragen', 'info');
            $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid . $backParam);
        } else {
          $this -> _flashMessenger -> addMessage('Abstimmung konnte nicht eingetragen werden (0)', 'info');
          $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid . $backParam);
        }
    }
    if (isset($votingSuccess['max'])) {
        $this -> _flashMessenger -> addMessage('Du hast schon zu oft diesen Button benutzt, bitte ändere zunächst folgende Votings. Diese Abstimmung wurde nicht gezählt!', 'info');
        $this -> redirect('/voting/preview/kid/' . $this -> _consultation -> kid. $backParam);
    }

}



  // function getBackparams () {
    // $param = $this -> getRequest() -> getParams();
    // $backParam = (!empty($param['qid'])) ? '/qid/' . $param['qid'] : '/tag/' . $param['tag'];
    // return $backParam;
  // }

  /**
   * update subusers votinglist
   */

  function votingChainSuccess($kid,$subUid,$tid,$backParam,$ajax= null) {
      // update subusers votinglist normal voting
      #if (!$ajax) {
        $votGroupModel = new Model_Votes_Groups();
        $votingChainSuccess = $votGroupModel -> excludeThesisFromVotingchain($kid,$subUid,$tid);
        if ($votingChainSuccess) {

          $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid . $backParam);
        } else {
          $this -> _flashMessenger -> addMessage('Abstimmung konnte nicht eingetragen werden (0)', 'info');
          $this -> redirect('/voting/vote/kid/' . $this -> _consultation -> kid . $backParam);
        }
      #} else {
        // update subusers votinglist voting preview
      #}
  }


  /**
   * user stop the voting, if user member of group, send confirm-email to this user
   */
  public function stopvotingAction() {
    $votingRightsSession = new Zend_Session_Namespace('votingRights');
    $userModel = new Model_Users();
    // Send mails to owner of group
    $uid = $votingRightsSession->uid;
    $subUid = $votingRightsSession->subUid;
    $user = $userModel->getById($uid);
    // user is member of group, send mail for his confirmation
    if($votingRightsSession->weight > 1 || $votingRightsSession->weight == 0) {
      $votingGroup = new Model_Votes_Groups();
      $subuser = $votingGroup->getByUser($uid, $subUid);

      $emailModel = new Model_Emails();
      $kid = $this->_consultation->kid;
      $authcode = $votingRightsSession->vtc;
      $confirmUrl = Zend_Registry::get('baseUrl') . '/voting/confirmvoting/kid/' . $kid . '/authcode/' . $authcode . '/user/' . $subUid;
      $accUrl = $confirmUrl . '/act/acc/';
      $rejUrl = $confirmUrl . '/act/rej/';
      $templateReplace = array(
        '{{KID_TITL}}'=>$this->_consultation->titl,
        '{{USER}}'=>$subuser['sub_user'],
        '{{URLCONFIRM}}'=>$accUrl,
        '{{URLREJCT}}'=>$rejUrl
      );

      $result = $emailModel->send($subuser['sub_user'], '-', '-', 'vot_conf', $templateReplace);
      if (!$result) {
        $logger = Zend_Registry::get('log');
        $logger -> debug('E-Mail für Voting-Bestätigung konnte nicht gesendet werden.');
      }

      $this->view->groupmember = $subuser['sub_user'];
    }
    // if singleuser (no group) just update status of his votes
    else {
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
  public function confirmvotingAction() {
    // action - what to do
    $act = $this->getRequest()->getParam('act');
    $subuid = $this->getRequest()->getParam('user');
    $authcode = $this->getRequest()->getParam('authcode');

    // action or subuid is not given
    if (($act != 'acc' && $act != 'rej') || empty($subuid) || empty($authcode)) {
      $this -> _flashMessenger -> addMessage('Der angew&auml;hlte Link aus ist nicht korrekt.', 'error');
      $this->redirect('/');
    }

    // get rights by authcode
    $votingRightModel = new Model_Votes_Rights();
    $votingRights = $votingRightModel->findByCode($authcode);

    // No access
    if (!$votingRights) {
      $this -> _flashMessenger -> addMessage('Der angew&auml;hlte Link aus ist nicht korrekt.', 'error');
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
    }
    // reject votes
    elseif ($act == 'rej') {
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

      $kid = $this->_consultation->kid;
      $url = Zend_Registry::get('baseUrl') . '/voting/confirmmember/kid/' . $kid . '/authcode/' . $authcode . '/user/' . $subuid;
      $urlConfirm = $url . '/act/' . md5($votingGroup['sub_user'] . $subuid . 'y');
      $urlReject = $url . '/act/' . md5($votingGroup['sub_user'] . $subuid . 'n');

      $emailModel = new Model_Emails();
      $templateReplace = array(
        '{{SITEURL}}'=>Zend_Registry::get('baseUrl'),
        '{{RECIPIENT}}'=>$leader['email'],
        '{{KID_TITL}}'=>$this->_consultation->titl,
        '{{KID}}'=>$this->_consultation->kid,
        '{{VOTER}}'=>$votingGroup['sub_user'],
        '{{VTC}}'=>$authcode,
        '{{SUB_UID}}'=>$subuid,
        '{{CONFIRMLINK}}'=>$urlConfirm,
        '{{REJECTLINK}}'=>$urlReject,
        '{{VOTERYEA}}'=>md5($votingGroup['sub_user'].$subuid.'y'),
        '{{VOTERNAY}}'=>md5($votingGroup['sub_user'].$subuid.'n')
      );

      $result = $emailModel->send($leader['email'], '-', '-', 'vot_grpmem_conf', $templateReplace);
      if (!$result) {
        $logger = Zend_Registry::get('log');
        $logger -> debug('E-Mail für Gruppenmitglieds-Bestätigung konnte nicht gesendet werden.');
      }
    }

    $this->view->act = $act;
    $this->view->memberstatus = $votingGroup['member'];
  }

  /**
   * Groupleader confirms member of his group
   */
  public function confirmmemberAction() {
    $act = $this->getRequest()->getParam('act');
    $subuid = $this->getRequest()->getParam('user');
    $authcode = $this->getRequest()->getParam('authcode');

    if(empty($act) || empty($subuid) || empty($authcode)) {
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

    $confirmCode = md5($votingGroup['sub_user'] . $subuid . 'y');
    $rejectCode = md5($votingGroup['sub_user'] . $subuid . 'n');

    // confirm
    if ($act == $confirmCode) {
      // set status of sub_user to 'y'
      $result = $votingGroupModel->confirmVoter($this->_consultation->kid, $votingRights['uid'], $subuid);
      if($result) {
        $this->view->act = 'confirm';
      }
    }
    elseif($act == $rejectCode) {
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

  public function resultsAction() {
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
