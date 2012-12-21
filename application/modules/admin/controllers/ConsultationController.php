<?php
/**
 * ConsultationController
 *
 * @desc   administrationareas
 * @author        Markus Hackel
 */
class Admin_ConsultationController extends Zend_Controller_Action {
  /**
   * @desc Construct
   * @return void
   */
  public function init() {
    // Setzen des Standardlayouts
    $this->_helper->layout->setLayout('backend');
  }

  /**
   * @desc consultation dashboard
   * @return void
   */
  public function indexAction() {
  }

  public function newAction() {
    $formClass = Zend_Registry::get('formloader')->load('Consultation');
    $form = new $formClass();
    
    $consultationModel = new Consultations();
    $lastId = $consultationModel->getLastId();
    $highestId = $lastId + 1;
    
    $form->getElement('ord')->setDescription(
      '(z.B. höher=weiter vorn; z.B. aktuelle Konsultationsnummer ' . $highestId . ')'
    );

    if ($this->getRequest()->isPost()
        && false !== $this->getRequest()->getPost('submit', false)) {
          if ($form->isValid($this->getRequest()->getPost())) {
            $consultationRow = $consultationModel->createRow($form->getValues());
            $consultationRow->save();
            
            $this->_redirect('admin/consultation/edit/kid/' . $consultationRow->kid);
          }
          else {
            $form->populate($this->getRequest()->getPost());
          }
    }

    $this->view->form = $form;
  }

  public function editAction() {
    $kid = $this->getRequest()->getParam('kid');
    
    $consultationModel = new Consultations();
    $consultationRow = $consultationModel->find($kid)->current();

    $lastId = $consultationModel->getLastId();
    $highestId = $lastId + 1;
    
    $formClass = Zend_Registry::get('formloader')->load('Consultation');
    $form = new $formClass();
    $form->setAction('/admin/consultation/edit/kid/' . $kid);
    
    $form->getElement('ord')->setDescription(
      '(z.B. höher=weiter vorn; z.B. neuer höchster Rang: ' . $highestId . ')'
    );
    
    if ($this->getRequest()->isPost()
        && false !== $this->getRequest()->getPost('submit', false)) {
          if ($form->isValid($this->getRequest()->getPost())) {
            $consultationRow->setFromArray($form->getValues());
            $consultationRow->save();
            
            $this->_redirect('admin/consultation/edit/kid/' . $consultationRow->kid);
          } else {
            $form->populate($form->getValues());
          }
    } else {
      $form->populate($consultationRow->toArray());
    }
    
    $this->view->form = $form;
  }
}
?>