<?php
/**
 * QuestionController
 *
 * @desc   Questions for Consultation
 * @author        Markus Hackel
 */
class Admin_QuestionController extends Zend_Controller_Action {
  
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
   * @desc show Questions Form
   * @return void
   */
  public function indexAction() {
    $kid = $this->getRequest()->getParam('kid', 0);
    $consultation = null;
    if ($kid > 0) {
      $consultationModel = new Model_Consultations();
      $consultation = $consultationModel->getById($kid);
      if (!empty($consultation)) {
        $this->view->consultation = $consultation;
      } else {
        $this->_redirect($this->_adminIndexURL, array('prependBase' => false));
      }
    } else {
      $this->_redirect($this->_adminIndexURL, array('prependBase' => false));
    }
  }
  
  public function createAction() {
    $kid = $this->getRequest()->getParam('kid', 0);
    $consultation = null;
    $form = null;
    if ($kid > 0) {
      $consultationModel = new Model_Consultations();
      $consultation = $consultationModel->getById($kid);
      if (!empty($consultation)) {
        $form = new Admin_Form_Question();
        $form->setAction($this->view->baseUrl() . '/admin/question/create/kid/' . $kid);
        if ($this->getRequest()->isPost()) {
          if ($form->isValid($this->getRequest()->getPost())) {
            $questionModel = new Model_Questions();
            // get max qi:
            $maxId = $questionModel->getMaxId();
            // create new qi:
            $newQi = intval($maxId)+rand(1,300);
            $questionRow = $questionModel->createRow($form->getValues());
            $questionRow->qi = $newQi;
            $questionRow->kid = $kid;
            $questionRow->ln = 'de';
            $newId = $questionRow->save();
            if ($newId > 0) {
              $this->_flashMessenger->addMessage('Neue Frage wurde erstellt.', 'success');
            } else {
              $this->_flashMessenger->addMessage('Erstellen neuer Frage fehlgeschlagen!', 'error');
            }
            
            $this->_redirect($this->view->url(array(
              'action' => 'index',
              'kid' => $kid
            )), array('prependBase' => false));
          } else {
            $form->populate($form->getValues());
          }
        }
      }
    }
    $this->view->assign(array(
      'consultation' => $consultation,
      'form' => $form
    ));
  }
  
  public function editAction() {
    $kid = $this->getRequest()->getParam('kid', 0);
    $consultation = null;
    $form = null;
    if ($kid > 0) {
      $consultationModel = new Model_Consultations();
      $consultation = $consultationModel->getById($kid);
      if (!empty($consultation)) {
        $qid = $this->getRequest()->getParam('qid', 0);
        if ($qid > 0) {
          $questionModel = new Model_Questions();
          $questionRow = $questionModel->find($qid)->current();
          $form = new Admin_Form_Question();
          if ($this->getRequest()->isPost()) {
            // Formular wurde abgeschickt und muss verarbeitet werden
            $params = $this->getRequest()->getPost();
            if ($form->isValid($params)) {
              $questionRow->setFromArray($form->getValues());
              $questionRow->save();
              $this->_flashMessenger->addMessage('Änderungen wurden gespeichert.', 'success');
              $question = $questionRow->toArray();
            } else {
              $this->_flashMessenger->addMessage('Bitte überprüfe die Eingaben und versuche es noch einmal!', 'error');
              $question = $params;
            }
          } else {
            $question = $questionModel->getById($qid);
          }
          $form->populate($question);
        }
      }
    }
    
    $this->view->assign(array(
      'consultation' => $consultation,
      'form' => $form
    ));
  }
  
  public function deleteAction() {
    $kid = $this->getRequest()->getParam('kid', 0);
    $qid = $this->getRequest()->getParam('qid', 0);
    if ($kid > 0 && $qid > 0) {
      $questionModel = new Model_Questions();
      $inputsModel = new Model_Inputs();
      $relatedInputs = $inputsModel->getByQuestion($qid);
      if (empty($relatedInputs)) {
        $nrDeleted = $questionModel->deleteById($qid);
        if ($nrDeleted > 0) {
          $this->_flashMessenger->addMessage('Die Frage wurde gelöscht.', 'success');
        }
      } else {
        $this->_flashMessenger->addMessage('Die Frage konnte nicht gelöscht werden, da bereits Beiträge dazu existieren.', 'error');
      }
    }
    $this->_redirect('/admin/question/index/kid/' . $kid);
  }
}
?>