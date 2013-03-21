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
   * form for access for voting, check email and authcode
   * @return void
   */
  public function indexAction() {
    $votingRightsSession = new Zend_Session_Namespace('votingRights');
    $form = new Default_Form_Voting_Authentification();
    // check if voting is in time
    $date = new Zend_Date();
    $nowDate = Zend_Date::now();
    if($nowDate->isEarlier($this->_consultation->vot_fr) || $nowDate->isLater($this->_consultation->vot_to)) {
      $this->_flashMessenger->addMessage('Derzeit ist es nicht möglich an der Abstimmung teilzunehmen.', 'info');
      $this->redirect('/');
    }
    // if session is allready created, forword to overview
    elseif($votingRightsSession->access) {
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
            if(empty($votingSubuser)) {
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
                throw new Exception('Fehler im Abstimmung. Bitte kontaktieren Sie den Administrator.');
              }
            }
            // we got a subuser
            else {
              // check if subuser is blocked
              if($votingSubuser['member']=='n') {
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
          }
          else {
            $this->_flashMessenger->addMessage('Ihre Eingaben sind nicht korrekt. Bitte pr&uuml;fen Sie diese.', 'error');
            $form->populate($data);
          }
        }
        // no access for voting
        else {
          $this->_flashMessenger->addMessage('Ihre Eingaben sind nicht korrekt. Bitte pr&uuml;fen Sie diese.', 'error');
          $form->populate($data);
        }
      }
      // invalid form
      else {
        $this->_flashMessenger->addMessage('Ihre Eingaben sind nicht korrekt. Bitte pr&uuml;fen Sie diese.', 'error');
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
    $this->view->votingExplanation = html_entity_decode($this->_consultation->vot_expl);
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
   * Voting filtered by tags or questions
   * note: need authentification over session
   */
  public function voteAction() {
    // no access, redirect back
    $votingRightsSession = new Zend_Session_Namespace('votingRights');
    if($votingRightsSession->access != $this->_consultation->kid) {
      $this->_flashMessenger->addMessage('In dieser Konsultation kann derzeit nicht abgestimmt werden.', 'error');
      $this->redirect('/');
    }
    
    $kid = $this->_consultation->kid;
    $qid = $this->getRequest()->getParam('qid');
    $tagId = $this->getRequest()->getParam('tag');
    $tid = $this->getRequest()->getParam('tid');
    $subUid = $votingRightsSession->subUid;
    
    if(empty($qid) && empty($tagId)) {
      $this->redirect('/voting/overview');
    }
    
    $this->view->qid = $qid;
    $this->view->tag = $tagId;
    if(!empty($tagId)) {
      $tagModel = new Model_Tags();
      $this->view->keyword = $tagModel->getNameById($tagId);
    }
    
    $questionModel = new Model_Questions();
    $inputModel = new Model_Inputs();
    $votingGroupModel = new Model_Votes_Groups();
    $votingIndividualModel = new Model_Votes_Individual();
    
    // Get thesis of this category
    $thesisList = (!empty($qid))
      ? $inputModel->getThesisbyQuestion($kid, $qid)
      : $inputModel->getThesisbyTag($kid, $tagId);
    
    
    
    // Count thesis of this category
    $thesisListCount = count($thesisList);
    $this->view->thesisCount = $thesisListCount;
    
    // Get thesis of this category which the user can vote
    $thesisListVotable = (!empty($qid))
      ? $votingGroupModel->getVotingListByQuestion($kid, $subUid, $qid)
      : $votingGroupModel->getVotingListByTag($kid, $subUid, $tagId);
    // Count thesis the user has allready voted
    $thesisCountVoted = $thesisListCount - count($thesisListVotable);
    $this->view->thesisCountVoted = $thesisCountVoted;

    // check if votable thesis are available
    if(($thesisCountVoted == $thesisListCount || $thesisListCount==0) && empty($tid)) {
      $this->view->noMoreThesis = true;
    }
    else {
      if(empty($tid)) {
        $tid = $thesisListVotable[rand(0, count($thesisListVotable)-1)];
      }
      // get thesis
      $thesis = $inputModel->getById($tid);
      $this->view->thesis = $thesis;
      // get question
      $question = $questionModel->getById($thesis['qi']);
      $questionTitle = $question['q'];

      $this->view->question = $questionTitle;
    }
    
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
      $this->_flashMessenger->addMessage('In dieser Konsultation kann derzeit nicht abgestimmt werden.', 'error');
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
    if($pts!=5 && $pts!=3 && $pts!=2 && $pts!=1 && $pts!=0) {
      $this->_flashMessenger->addMessage('Die vergebenen Punkte sind nicht korrekt', 'info');
      $this->redirect('/voting/vote/kid/'. $this->_consultation['kid'] . $backParam);
    }

    $votIndiModel = new Model_Votes_Individual();
    $votingSuccess = $votIndiModel->updateVote($param['tid'], $subUid, $uid, $pts);
    if($votingSuccess) {
      // update subusers votinglist
      $votGroupModel = new Model_Votes_Groups();
      $votingChainSuccess = $votGroupModel->excludeThesisFromVotingchain($this->_consultation['kid'], $subUid, $param['tid']);
      if($votingChainSuccess) {
        $this->redirect('/voting/vote/kid/'. $this->_consultation['kid'] . $backParam);
      }
      else {
        $this->_flashMessenger->addMessage('Abstimmung konnte nicht eingetragen werden (0)', 'info');
        $this->redirect('/voting/vote/kid/'. $this->_consultation['kid'] . $backParam);
      }
    }
    else {
      $this->_flashMessenger->addMessage('Abstimmung konnte nicht eingetragen werden (1)', 'info');
      $this->redirect('/voting/vote/kid/'. $this->_consultation['kid'] . '/tid/' . $param['tid'] . $backParam);
    }
    
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
      $confirmUrl = Zend_Registry::get('baseUrl').'/voting/confirmvoting/kid/'
      .$kid.'/authcode/'.$authcode.'/user/'.$subUid;
      $accUrl = $confirmUrl.'/act/acc/';
      $rejUrl = $confirmUrl.'/act/rej/';
      $templateReplace = array(
        '{{KID_TITL}}'=>$this->_consultation->titl,
        '{{USER}}'=>$subuser['sub_user'],
        '{{URLCONFIRM}}'=>$accUrl,
        '{{URLREJCT}}'=>$rejUrl
      );
      
      $result = $emailModel->send($subuser['sub_user'], '-','-','vot_conf',$templateReplace);
      if(!$result) {
        $logger = Zend_Registry::get('log');
        $logger->debug('E-Mail für Voting-Bestätigung konnte nicht gesendet werden.');
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
    if(($act != 'acc' && $act != 'rej') || empty($subuid) || empty($authcode)) {
      $this->_flashMessenger->addMessage('Der angew&auml;hlte Link aus ist nicht korrekt.', 'error');
      $this->redirect('/');
    }
    
    // get rights by authcode
    $votingRightModel = new Model_Votes_Rights();
    $votingRights = $votingRightModel->findByCode($authcode);
    
    // No access
    if(!$votingRights) {
      $this->_flashMessenger->addMessage('Der angew&auml;hlte Link aus ist nicht korrekt.', 'error');
      $this->redirect('/');
    }
    
    // get group
    $votingGroupModel = new Model_Votes_Groups();
    $votingGroup = $votingGroupModel->getByUser($votingRights['uid'], $subuid);
    
    // confirm
    $votingIndivModel = new Model_Votes_Individual();
    if($act == 'acc') {
      // If user is singleuser (not group)
      if($votingRights['vt_weight']>1 || $votingRights['vt_weight']==1) {
        $result = $votingIndivModel->setStatusForSubuser($votingRights['uid'], $subuid, 'v', 'c');
      }
      $this->view->heading = 'Deine Bewertungen sind jetzt bestätigt.';
    }
    // reject votes
    elseif($act == 'rej') {
      // If user is singleuser (not group)
      if($votingRights['vt_weight']>1 || $votingRights['vt_weight']==1) {
        $result = $votingIndivModel->deleteByStatusForSubuser($votingRights['uid'], $subuid, 'v');
      }
    }
    
    // send mail to singleuser/groupleader if user in unconfirmed
    if($votingGroup['member'] == 'u') {
      // get groupleader
      $userModel = new Model_Users();
      $leader = $userModel->getById($votingGroup['uid']);
      
      $kid = $this->_consultation->kid;
      $url = Zend_Registry::get('baseUrl') . '/voting/confirmmember/kid/' . $kid . '/authcode/'
      . $authcode . '/user/' . $subuid;
      $urlConfirm = $url . '/act/' . md5($votingGroup['sub_user'].$subuid.'y');
      $urlReject = $url . '/act/' . md5($votingGroup['sub_user'].$subuid.'n');
      
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
      
      $result = $emailModel->send($leader['email'], '-','-','vot_grpmem_conf',$templateReplace);
      if(!$result) {
        $logger = Zend_Registry::get('log');
        $logger->debug('E-Mail für Gruppenmitglieds-Bestätigung konnte nicht gesendet werden.');
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
    if(!$votingRights) {
      $this->_flashMessenger->addMessage('Der angew&auml;hlte Link aus ist nicht korrekt.', 'error');
      $this->redirect('/');
    }
    
    // get group
    $votingGroupModel = new Model_Votes_Groups();
    $votingGroup = $votingGroupModel->getByUser($votingRights['uid'], $subuid);
    
    $confirmCode = md5($votingGroup['sub_user'].$subuid.'y');
    $rejectCode = md5($votingGroup['sub_user'].$subuid.'n');
    
    // confirm
    if($act == $confirmCode) {
      // set status of sub_user to 'y'
      $result = $votingGroupModel->confirmVoter($this->_consultation->kid, $votingRights['uid'], $subuid);
      if($result) {
        $this->view->act = 'confirm';
      }
    }
    elseif($act == $rejectCode) {
      // set status of sub_user to 'n'
      $result = $votingGroupModel->denyVoter($this->_consultation->kid, $votingRights['uid'], $subuid);
      if($result) {
        $this->view->act = 'reject';
      }
    }
    else {
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
