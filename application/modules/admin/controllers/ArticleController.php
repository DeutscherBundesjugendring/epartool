<?php
/**
 * ArticleController
 *
 * @desc   Articles for Consultation
 * @author        Markus Hackel
 */
class Admin_ArticleController extends Zend_Controller_Action {
  
  protected $_flashMessenger = null;
  
  protected $_adminIndexURL = null;
  
  /**
   * @desc Construct
   * @return void
   */
  public function init() {
    // Setzen des Standardlayouts
    $this->_helper->layout->setLayout('backend');
    $this->_flashMessenger =
        $this->_helper->getHelper('FlashMessenger');
    $this->initView();
    $this->_adminIndexURL = $this->view->url(array(
      'controller' => 'index',
      'action' => 'index'
    ));
  }

  /**
   * @desc show Articles Form
   * @return void
   */
  public function indexAction() {
    $kid = $this->getRequest()->getParam('kid', 0);
    $consultation = null;
    $articles = null;
    if ($kid > 0) {
      $consultationModel = new Consultations();
      $consultation = $consultationModel->getById($kid);
      if (!empty($consultation)) {
        $this->view->consultation = $consultation;
        $articles = $consultation['articles'];
      } else {
        $this->_redirect($this->_adminIndexURL);
      }
    } else {
      $articleModel = new Articles();
      $articles = $articleModel->getAllWithoutConsultation();
//      $this->_redirect($this->_adminIndexURL);
    }
    $this->view->articles = $articles;

    $this->view->messages = $this->getCollectedMessages();
  }
  
  public function createAction() {
    $kid = $this->getRequest()->getParam('kid', 0);
    $consultation = null;
    $form = null;
//    if ($kid > 0) {
//    }
    $consultationModel = new Consultations();
    $consultation = $consultationModel->getById($kid);
//    if (!empty($consultation)) {
//    }
    $formClass = Zend_Registry::get('formloader')->load('Article');
    $form = new $formClass();
    $form->setAction('/admin/article/create/kid/' . $kid);
    if ($this->getRequest()->isPost()) {
      if ($form->isValid($this->getRequest()->getPost())) {
        $articleModel = new Articles();
        $articleRow = $articleModel->createRow($form->getValues());
        $articleRow->kid = $kid;
        $newId = $articleRow->save();
        if ($newId > 0) {
          $this->_flashMessenger->addMessage('Neuer Artikel wurde erstellt.', 'success');
        } else {
          $this->_flashMessenger->addMessage('Erstellen des neuen Artikels fehlgeschlagen!', 'error');
        }
        
        $this->_redirect($this->view->url(array(
          'action' => 'index',
          'kid' => $kid
        )));
      } else {
        $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
        $form->populate($form->getValues());
      }
    }
    $this->view->assign(array(
      'messages' => $this->getCollectedMessages(),
      'kid' => $kid,
      'consultation' => $consultation,
      'form' => $form
    ));
  }
  
  public function editAction() {
    $kid = $this->getRequest()->getParam('kid', 0);
    $consultation = null;
    $form = null;
//    if ($kid > 0) {
//    }
    $consultationModel = new Consultations();
    $consultation = $consultationModel->getById($kid);
//    if (!empty($consultation)) {
//    }
    $aid = $this->getRequest()->getParam('aid', 0);
    if ($aid > 0) {
      $articleModel = new Articles();
      $articleRow = $articleModel->find($aid)->current();
      $formClass = Zend_Registry::get('formloader')->load('Article');
      $form = new $formClass();
      if ($this->getRequest()->isPost()) {
        // Formular wurde abgeschickt und muss verarbeitet werden
        $params = $this->getRequest()->getPost();
        if ($form->isValid($params)) {
          $articleRow->setFromArray($form->getValues());
          $articleRow->save();
          $this->_flashMessenger->addMessage('Änderungen wurden gespeichert.', 'success');
          $article = $articleRow->toArray();
        } else {
          $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben und versuchen Sie es erneut!', 'error');
          $article = $params;
        }
      } else {
        $article = $articleModel->getById($aid);
      }
      $form->populate($article);
    }
    
    $this->view->assign(array(
      'messages' => $this->getCollectedMessages(),
      'kid' => $kid,
      'consultation' => $consultation,
      'form' => $form
    ));
  }
  
  public function deleteAction() {
    $kid = $this->getRequest()->getParam('kid', 0);
    $aid = $this->getRequest()->getParam('aid', 0);
    if ($aid > 0) {
      $articleModel = new Articles();
      $articleRow = $articleModel->getById($aid);
      if ($articleRow['kid'] == $kid) {
        $nrDeleted = $articleModel->deleteById($aid);
        if ($nrDeleted > 0) {
          $this->_flashMessenger->addMessage('Der Artikel wurde gelöscht.', 'success');
        }
      }
    }
    $this->_redirect('/admin/article/index/kid/' . $kid);
  }
  
  protected function getCollectedMessages($clearCurrent = true) {
    $aMessages = array(
      'success' => $this->_flashMessenger->getMessages('success'),
      'error' => $this->_flashMessenger->getMessages('error')
    );
    $aCurrentMessages['success'] = $this->_flashMessenger->getCurrentMessages('success');
    $aMessages['success'] = array_merge($aMessages['success'], $aCurrentMessages['success']);
    $aCurrentMessages['error'] = $this->_flashMessenger->getCurrentMessages('error');
    $aMessages['error'] = array_merge($aMessages['error'], $aCurrentMessages['error']);
    if ($clearCurrent) {
      // clear current messages to prevent them from showing in next request
      $this->_flashMessenger->setNamespace('success')->clearCurrentMessages();
      $this->_flashMessenger->setNamespace('error')->clearCurrentMessages();
    }
    
    return $aMessages;
  }
}
?>