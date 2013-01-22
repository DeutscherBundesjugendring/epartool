<?php
/**
 * ConsultationController
 *
 * @desc   administrationareas
 * @author        Markus Hackel
 */
class Admin_ConsultationController extends Zend_Controller_Action {
  
  protected $_flashMessenger = null;
  
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
  }

  /**
   * @desc consultation dashboard
   * @return void
   */
  public function indexAction() {
  }

  public function newAction() {
    $form = new Admin_Form_Consultation();
    
    if ($this->getRequest()->isPost()
        && false !== $this->getRequest()->getPost('submit', false)) {
          if ($form->isValid($this->getRequest()->getPost())) {
            $consultationRow = $consultationModel->createRow($form->getValues());
            $newId = $consultationRow->save();
            if ($newId > 0) {
              $this->_flashMessenger->addMessage('Neue Konsultation wurde erstellt.', 'success');
              $this->_redirect('admin/consultation/edit/kid/' . $consultationRow->kid);
            } else {
              $this->_flashMessenger->addMessage('Erstellen der neuen Konsultation fehlgeschlagen!', 'error');
            }
          } else {
            $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
            $form->populate($this->getRequest()->getPost());
          }
    }
    $this->view->messages = $this->getCollectedMessages();

    $this->view->form = $form;
  }

  public function editAction() {
    $kid = $this->getRequest()->getParam('kid');
    
    $consultationModel = new Model_Consultations();
    $consultationRow = $consultationModel->find($kid)->current();

    $form = new Admin_Form_Consultation();
    $form->setAction('/admin/consultation/edit/kid/' . $kid);
    
    if ($this->getRequest()->isPost()
        && false !== $this->getRequest()->getPost('submit', false)) {
          if ($form->isValid($this->getRequest()->getPost())) {
            $consultationRow->setFromArray($form->getValues());
            $consultationRow->save();
            $this->_flashMessenger->addMessage('Änderungen gespeichert.', 'success');
            
            $this->_redirect('admin/consultation/edit/kid/' . $consultationRow->kid);
          } else {
            $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
            $form->populate($form->getValues());
          }
    } else {
      $form->populate($consultationRow->toArray());
    }
    
    $this->view->messages = $this->getCollectedMessages();
    
    $this->view->form = $form;
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