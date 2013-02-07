<?php
/**
 * ConsultationController
 *
 * @desc   administrationareas
 * @author        Markus Hackel
 */
class Admin_ConsultationController extends Zend_Controller_Action {
  
  protected $_flashMessenger = null;
  
  protected $_consultation = null;
  
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
    
    $kid = $this->getRequest()->getParam('kid');
    $consultationModel = new Model_Consultations();
    $this->_consultation = $consultationModel->find($kid)->current();
    $this->view->consultation = $this->_consultation;
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

    $this->view->form = $form;
  }

  public function editAction() {
    $form = new Admin_Form_Consultation();
    $form->setAction('/admin/consultation/edit/kid/' . $this->_consultation->kid);
    
    if ($this->getRequest()->isPost()
        && false !== $this->getRequest()->getPost('submit', false)) {
          if ($form->isValid($this->getRequest()->getPost())) {
            $this->_consultation->setFromArray($form->getValues());
            $this->_consultation->save();
            $this->_flashMessenger->addMessage('Änderungen gespeichert.', 'success');
            
            $this->_redirect('admin/consultation/edit/kid/' . $this->_consultation->kid);
          } else {
            $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
            $form->populate($form->getValues());
          }
    } else {
      $form->populate($this->_consultation->toArray());
    }
    
    $this->view->form = $form;
  }
}
?>