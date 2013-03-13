<?php

class Admin_VotingController extends Zend_Controller_Action {
  
  protected $_flashMessenger = null;
  
  protected $_consultation = null;
  
  /**
   * Construct
   * @return void
   */
  public function init() {
    // Setzen des Standardlayouts
    $this->_helper->layout->setLayout('backend');
    $this->_flashMessenger =
        $this->_helper->getHelper('FlashMessenger');
    $kid = $this->_request->getParam('kid', 0);
    if ($kid > 0) {
      $consultationModel = new Model_Consultations();
      $consultation = $consultationModel->getById($kid);
      $this->_consultation = $consultation;
      $this->view->consultation = $consultation;
    } else {
      $this->_flashMessenger->addMessage('Keine Konsultation angegeben!', 'error');
      $this->redirect('/admin');
    }
  }
  
  /**
   * List Voting Rights
   *
   */
  public function indexAction() {
    $votingRightsModel = new Model_Votes_Rights();
    $this->view->countInserted = $votingRightsModel
      ->setInitialRightsByConsultation($this->_consultation['kid']);
    $this->view->votingRights = $votingRightsModel
      ->getByConsultation($this->_consultation['kid']);
  }
  
  /**
   * Edit Voting Rights
   *
   */
  public function editrightsAction() {
    $uid = $this->_request->getParam('uid', 0);
    if ($uid > 0) {
      $userModel = new Model_Users();
      $votingRightsModel = new Model_Votes_Rights();
      $form = new Admin_Form_Voting_Rights();
      
      $user = $userModel->getById($uid);
      $votingRights = $votingRightsModel
        ->getByUserAndConsultation($uid, $this->_consultation['kid']);
      
      if ($this->_request->isPost()) {
        // form sent -> process
        $data = $this->_request->getPost();
        if ($form->isValid($data)) {
          $votingRights->setFromArray($data)->save();
          $this->_flashMessenger->addMessage('Änderungen für <b>' . $user['email'] . '</b> gespeichert.', 'success');
          $this->redirect($this->view->url(array(
            'action' => 'index',
            'uid' => null,
          )), array('prependBase' => false));
        } else {
          $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
        }
      } else {
        // form not submitted, initial request
        $data = array(
          'vt_weight' => $votingRights['vt_weight'],
          'vt_code' => $votingRights['vt_code'],
          'grp_siz' => $votingRights['grp_siz'],
          'group_size_user' => $user['group_size'],
        );
      }
      $form->populate($data);
      
      $this->view->assign(array(
        'form' => $form,
        'user' => $user
      ));
    } else {
      $this->_flashMessenger->addMessage('Keine User ID angegeben!', 'error');
      $this->redirect('/admin/voting');
    }
  }
  
  /**
   * List paricipants for invitation
   *
   */
  public function invitationsAction() {
    $userModel = new Model_Users();
    $votingRightsModel = new Model_Votes_Rights();
    $participants = $userModel->getParticipantsByConsultation(
      $this->_consultation['kid'],
      array('u.email', 'u.name')
    );
    $emailList = '';
    foreach($participants as $key => $value) {
      if (!empty($value['email'])) {
        $emailList.= $value['email'] . ';';
      }
      $votingRights = $votingRightsModel
        ->getByUserAndConsultation($value['uid'], $this->_consultation['kid']);
      if (!empty($votingRights)) {
        $participants[$key]['votingRights'] = $votingRights;
      }
    }
    $this->view->assign(array(
      'participants' => $participants,
      'emailList' => $emailList,
    ));
  }
  
  /**
   * Send voting invitation by email via preview or directly
   *
   */
  public function sendinvitationAction() {
    $uid = $this->_request->getParam('uid', 0);
    $mode = $this->_request->getParam('mode');
    
    if ($uid > 0) {
      $date = new Zend_Date();
      $userModel = new Model_Users();
      $votingRightsModel = new Model_Votes_Rights();
      $emailModel = new Model_Emails();
      $emailTemplateModel = new Model_Emails_Templates();
      $form = new Admin_Form_Email_Send();
      $form->setAction($this->view->url());
      $formSent = false;
      $sentFromPreview = false;
      $systemconfig = Zend_Registry::get('systemconfig');
      $grp_siz_def = $systemconfig->group_size_def->toArray();
      
      // set defaults for given user:
      $user = $userModel->getById($uid);
      $votingRights = $votingRightsModel->getByUserAndConsultation($uid, $this->_consultation['kid']);
      $receiver = $user['email'];
      $cc = '';
      $bcc = '';
      if ($votingRights['vt_weight'] != 1) {
        // group type is group
        $templateRef = 'vt_invit_group';
      } else {
        // group type is single
        $templateRef = 'vt_invit_single';
      }
      // prepare marker array
      $templateReplace = array(
        '{{USER}}' => (empty($user['name']) ? $user['email'] : $user['name']),
        '{{CNSLT_TITLE}}' => $this->_consultation['titl'],
        '{{VOTE_FROM}}' => $date->set($this->_consultation['vot_fr'])->get(Zend_Date::DATE_MEDIUM),
        '{{VOTE_TO}}' => $date->set($this->_consultation['vot_to'])->get(Zend_Date::DATE_MEDIUM),
        '{{SITEURL}}' => Zend_Registry::get('baseUrl') . '/voting/index/authcode/',
        '{{VTC}}' => $votingRights['vt_code'],
        '{{GROUP_CATEGORY}}' => $grp_siz_def[$votingRights['grp_siz']],
        '{{VOTING_WEIGHT}}' => $votingRights['vt_weight'],
      );
      $templateRow = $emailTemplateModel->getByName($templateRef);
      $subject = '';
      $message = '';
      if (!empty($templateRow)) {
        // work with email template
        $subject = $templateRow->subj;
        $message = $templateRow->txt;
        // replace markers
        foreach ($templateReplace as $search => $replace) {
          $subject = str_replace($search, $replace, $subject);
          $message = str_replace($search, $replace, $message);
        }
        // use head-Area
        if($templateRow->head=='y') {
          $templateHeader = $emailTemplateModel->getByName('header');
          // if head-template exists
          if($templateHeader) {
            $message = $templateHeader->txt . $message;
          }
        }
        // use footer-Area
        if($templateRow->foot=='y') {
          $templateFooter = $emailTemplateModel->getByName('footer');
          // if head-template exists
          if($templateFooter) {
            $message.= $templateFooter->txt;
          }
        }
      } else {
        // no template found, give chance to write email manually
        $mode = 'preview';
        $this->_flashMessenger->addMessage('Kein E-Mail Template gefunden!', 'error');
      }
      
      // check if form from preview is submitted
      if ($this->_request->isPost()) {
        $formSent = true;
        // sent from preview
        $data = $this->_request->getPost();
        if ($form->isValid($data)) {
          // mail can be sent directly
          $mode = 'instantsend';
          $sentFromPreview = true;
        } else {
          $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
          $form->populate($data);
        }
      }
      
      switch ($mode) {
        case 'instantsend':
          // send mail directly
          if ($sentFromPreview) {
            // use data from preview
            $mailData = $form->getValues();
            $receiver = $mailData['empfaenger'];
            $cc = $mailData['mailcc'];
            $bcc = $mailData['mailbcc'];
            $subject = $mailData['subject'];
            $message = $mailData['message'];
            $templateRef = null;
            $templateReplace = null;
          } else {
            // use user defaults, see above
          }
          
          $sent = $emailModel->send($receiver, $subject, $message, $templateRef, $templateReplace, null, null, $cc, $bcc);
          // redirect to overview
          if ($sent) {
            $this->_flashMessenger->addMessage('Votingeinladung an <b>' . $receiver . '</b> versendet.', 'success');
          } else {
            $this->_flashMessenger->addMessage('Beim Senden der E-Mail ist ein Fehler aufgetreten.', 'error');
          }
          $this->redirect('/admin/voting/invitations/kid/' . $this->_consultation['kid']);
          break;
          
        case 'preview':
        default:
          if (!$formSent) {
            // show form for the first time, fill with default data (see above)
            $form->getElement('empfaenger')->setValue($receiver);
            $form->getElement('subject')->setValue($subject);
            $form->getElement('message')->setValue($message);
          }
          // assign view variables and render view script for this action
          $this->view->form = $form;
          break;
      }
      
    } else {
      $this->_flashMessenger->addMessage('Kein Nutzer angegeben!', 'error');
      $this->redirect('/admin/voting/invitations/kid/' . $this->_consultation['kid']);
    }
  }
  
  /**
   * List voters
   *
   */
  public function participantsAction() {
    $groupsModel = new Model_Votes_Groups();
    
    $this->view->groups = $groupsModel->getByConsultation($this->_consultation['kid']);
  }
  
  /**
   * Deny voter
   *
   */
  public function participantdenyAction() {
    $uid = $this->_request->getParam('uid', 0);
    $sub_uid = $this->_request->getParam('sub_uid', 0);
    $votesGroupsModel = new Model_Votes_Groups();
    
    if ($votesGroupsModel->denyVoter($this->_consultation['kid'], $uid, $sub_uid)) {
      $this->_flashMessenger->addMessage('Teilnehmer wurde abgelehnt.', 'success');
    } else {
      $this->_flashMessenger->addMessage('Ablehnen fehlgeschlagen.', 'error');
    }
     
    $this->redirect('/admin/voting/participants/kid/' . $this->_consultation['kid']);
  }
  
  /**
   * Confirm voter
   *
   */
  public function participantconfirmAction() {
    $uid = $this->_request->getParam('uid', 0);
    $sub_uid = $this->_request->getParam('sub_uid', 0);
    $votesGroupsModel = new Model_Votes_Groups();
    
    if ($votesGroupsModel->confirmVoter($this->_consultation['kid'], $uid, $sub_uid)) {
      $this->_flashMessenger->addMessage('Teilnehmer wurde bestätigt.', 'success');
    } else {
      $this->_flashMessenger->addMessage('Bestätigen fehlgeschlagen.', 'error');
    }
    
    $this->redirect('/admin/voting/participants/kid/' . $this->_consultation['kid']);
  }
  
  /**
   * Delete voter
   *
   */
  public function participantdeleteAction() {
    $uid = $this->_request->getParam('uid', 0);
    $sub_uid = $this->_request->getParam('sub_uid', 0);
    $votesGroupsModel = new Model_Votes_Groups();
    
    if ($votesGroupsModel->deleteVoter($this->_consultation['kid'], $uid, $sub_uid) > 0) {
      $this->_flashMessenger->addMessage('Teilnehmer wurde gelöscht.', 'success');
    } else {
      $this->_flashMessenger->addMessage('Löschen fehlgeschlagen.', 'error');
    }
    
    $this->redirect('/admin/voting/participants/kid/' . $this->_consultation['kid']);
  }
  
  public function resultsAction() {
    $qid = $this->_request->getParam('qid', 0);
    $theses_votes = array();
    $theses_votes_order = array();
    $theses_values = array();
    $questionModel = new Model_Questions();
    $questions = $questionModel->getByConsultation($this->_consultation['kid']);
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
        ->getVotingValuesByThesis($thesis['tid'], $this->_consultation['kid']);
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
    
    $this->view->assign(array(
      'currentQuestion' => $currentQuestion,
      'questions' => $questions,
      'theses_votes' => $theses_votes,
      'highest_rank' => key($theses_votes)
    ));
  }
  
}
?>