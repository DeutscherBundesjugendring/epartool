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
      $this->redirect('/admin/voting');
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
          )));
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
  
  public function invitationsAction() {
    
  }
  
  public function participantsAction() {
    
  }
  
  public function resultsAction() {
    
  }
  
}
?>