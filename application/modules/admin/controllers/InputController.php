<?php

class Admin_InputController extends Zend_Controller_Action {
  
  protected $_flashMessenger = null;
  
  protected $_consultation = null;
  
  public function init() {
    // Setzen des Standardlayouts
    $this->_helper->layout->setLayout('backend');
    $this->_flashMessenger =
        $this->_helper->getHelper('FlashMessenger');
    $kid = $this->_request->getParam('kid', 0);
    if ($kid > 0) {
      $consultationModel = new Model_Consultations();
      $this->_consultation = $consultationModel->getById($kid);
      $this->view->consultation = $this->_consultation;
    }
  }
  
  /**
   * List of all Inputs by Question, optionally filtered by Tag
   *
   */
  public function indexAction() {
    $qid = $this->_request->getParam('qid', 0);
    $tid = $this->_request->getParam('tid', 0);
    $tag = $this->_request->getParam('tag', 0);
    $questionModel = new Model_Questions();
    $tagModel = new Model_Tags();
    
    if ($qid > 0) {
      $this->view->question = $questionModel->getById($qid, $tag);
    }
    
    if ($tag > 0) {
      $this->view->tag = $tagModel->getById($tag);
    }
    $this->view->tid = $tid;
    $this->view->qid = $qid;
  }
  
  /**
   * List of all Inputs of a Consultation by User
   *
   */
  public function listAction() {
    $kid = $this->_request->getParam('kid', 0);
    $uid = $this->_request->getParam('uid', 0);
    $userModel = new Model_Users();
    $questionModel = new Model_Questions();
    
    $this->view->assign(array(
      'kid' => $kid,
      'user' => $userModel->getById($uid),
      'questions' => $questionModel->getWithInputsByUser($uid, $kid),
    ));
  }
  
  /**
   * List of all Users who participated in a Consultation
   *
   */
  public function userlistAction() {
    $kid = $this->_request->getParam('kid', 0);
    $userModel = new Model_Users();
    $this->view->users = $userModel->getParticipantsByConsultation($kid);
  }
  
  /**
   * Edit Input
   *
   */
  public function editAction() {
    $tid = $this->_request->getParam('tid', 0);
    $qid = $this->_request->getParam('qid', 0);
    $inputModel = new Model_Inputs();
    $form = new Admin_Form_Input();
    
    if ($this->_request->isPost()) {
      $data = $this->_request->getPost();
      if ($form->isValid($data)) {
        $updated = $inputModel->updateById($tid, $form->getValues());
        if ($updated == $tid) {
          $this->_flashMessenger->addMessage('Eintrag aktualisiert', 'success');
        } else {
          $this->_flashMessenger->addMessage('Aktualisierung fehlgeschlagen', 'error');
        }
      } else {
        $this->_flashMessenger->addMessage('Bitte Eingaben prüfen!', 'error');
        $form->populate($data);
      }
    } else {
      $inputRow = $inputModel->getById($tid);
      $form->populate($inputRow);
      if (!empty($inputRow['tags'])) {
        // gesetzte Tags als selected markieren
        $tagsSet = array();
        foreach ($inputRow['tags'] as $tag) {
          $tagsSet[] = $tag['tg_nr'];
        }
        $form->setDefault('tags', $tagsSet);
      }
    }
    
    $this->view->assign(array(
      'form' => $form,
      'qid' => $qid,
      'tid' => $tid
    ));
  }
  
  /**
   * Edit Inputs in bulk
   *
   */
  public function editbulkAction() {
    if (!$this->_request->isPost()) {
      $this->_flashMessenger->addMessage('Ungültiger Aufruf!', 'error');
      $this->redirect('/admin');
    }
    $inputModel = new Model_Inputs();
    $data = $this->_request->getPost();
    switch ($data['action']) {
      case 'delete':
        $nr = $inputModel->deleteBulk($data['inp_list']);
        if ($nr > 0) {
          $this->_flashMessenger->addMessage($nr . ' Beiträge gelöscht!', 'success');
        }
        break;
      case 'block':
        $inputModel->editBulk($data['inp_list'], array('block' => 'y'));
        $this->_flashMessenger->addMessage(count($data['inp_list']) . ' Beiträge gesperrt!', 'success');
        break;
      case 'publish':
        $inputModel->editBulk($data['inp_list'], array('block' => 'n'));
        $this->_flashMessenger->addMessage(count($data['inp_list']) . ' Beiträge freigegeben!', 'success');
        break;
      default:
        $this->_flashMessenger->addMessage('Bitte eine Aktion auswählen!', 'error');
        break;
    }
    
    $url = $data['return_url'];
    if (empty($url)) {
      $url = '/admin';
    }
    $this->redirect($url);
  }
}
?>