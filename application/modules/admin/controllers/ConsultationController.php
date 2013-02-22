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

  /**
   * create new Consultation
   *
   */
  public function newAction() {
    $form = new Admin_Form_Consultation();
    
    if ($this->getRequest()->isPost()
        && false !== $this->getRequest()->getPost('submit', false)) {
          $consultationModel = new Model_Consultations();
          if ($form->isValid($this->getRequest()->getPost())) {
            $consultationRow = $consultationModel->createRow($form->getValues());
            $newId = $consultationRow->save();
            if ($newId > 0) {
              $this->_flashMessenger->addMessage('Neue Konsultation wurde erstellt.', 'success');
              $this->_redirect('/admin/consultation/edit/kid/' . $consultationRow->kid);
            } else {
              $this->_flashMessenger->addMessage('Erstellen der neuen Konsultation fehlgeschlagen!', 'error');
            }
          } else {
            $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
            $form->populate($this->getRequest()->getPost());
          }
    }
    
    foreach ($form->getElements() as $element) {
      $element->clearFilters();
      $element->setValue(html_entity_decode($element->getValue()));
    }

    $this->view->form = $form;
  }

  /**
   * edit Consultation settings
   *
   */
  public function editAction() {
    $form = new Admin_Form_Consultation();
    $form->setAction('/admin/consultation/edit/kid/' . $this->_consultation->kid);
    
    if ($this->getRequest()->isPost()
        && false !== $this->getRequest()->getPost('submit', false)) {
          if ($form->isValid($this->getRequest()->getPost())) {
            $this->_consultation->setFromArray($form->getValues());
            $this->_consultation->save();
            $this->_flashMessenger->addMessage('Änderungen gespeichert.', 'success');
            
            $this->_redirect('/admin/consultation/edit/kid/' . $this->_consultation->kid);
          } else {
            $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
            $form->populate($form->getValues());
          }
    } else {
      $form->populate($this->_consultation->toArray());
    }
    
    foreach ($form->getElements() as $element) {
      $element->clearFilters();
      $element->setValue(html_entity_decode($element->getValue()));
    }
    
    $this->view->form = $form;
  }
  
  /**
   * statistical Report
   *
   */
  public function reportAction() {
    $kid = $this->_request->getParam('kid', 0);
    if (empty($kid)) {
      $this->_flashMessenger->addMessage('Keine Konsultation angegeben!', 'error');
      $this->redirect('/admin');
    }
    $inputsModel = new Model_Inputs();
    $questionModel = new Model_Questions();
    
    $questionRowset = $questionModel->getByConsultation($kid);
    $questions = array();
    foreach ($questionRowset as $question) {
      $question = $question->toArray();
      $questions[$question['qi']] = $question;
      $questions[$question['qi']]['nrInputsConfirmed'] = $inputsModel
        ->getCountByQuestionFiltered($question['qi'], array(
          array(
            'field' => 'user_conf',
            'operator' => '=',
            'value' => 'c'
          )
        ));
      $questions[$question['qi']]['nrInputsVoting'] = $inputsModel
        ->getCountByQuestionFiltered($question['qi'], array(
          array(
            'field' => 'vot',
            'operator' => '=',
            'value' => 'y'
          )
        ));
    }
    
    $this->view->assign(array(
      'nrParticipants' => $inputsModel->getCountParticipantsByConsultation($kid),
      'nrInputs' => $inputsModel->getCountByConsultation($kid, false),
      'nrInputsConfirmed' => $inputsModel->getCountByConsultationFiltered($kid,
        array(array('field' => 'user_conf', 'operator' => '=', 'value' => 'c'))),
      'nrInputsUnconfirmed' => $inputsModel->getCountByConsultationFiltered($kid,
        array(array('field' => 'user_conf', 'operator' => '=', 'value' => 'u'))),
      'nrInputsBlocked' => $inputsModel->getCountByConsultationFiltered($kid,
        array(array('field' => 'block', 'operator' => '=', 'value' => 'y'))),
      'nrInputsVoting' => $inputsModel->getCountByConsultationFiltered($kid,
        array(array('field' => 'vot', 'operator' => '=', 'value' => 'y'))),
      'questions' => $questions
    ));
  }
}
?>