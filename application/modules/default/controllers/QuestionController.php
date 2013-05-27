<?php
/**
 * QuestionController
 * @desc     Fragen zur Konsultation
 * @author        Markus Hackel
 */
class QuestionController extends Zend_Controller_Action {

  protected $_user = null;
  
  protected $_consultation = null;

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
  }
  /**
   * index
   * @desc Übersicht der Fragen zur Konsultation
   * @return void
   */
  public function indexAction() {
    $questionModel = new Model_Questions();
    $questions = $questionModel->getByConsultation($this->_consultation->kid);
    $this->view->questions = $questions;
  }
  
  /**
   * Show single Question
   *
   */
//   public function showAction() {
//     $qid = $this->getRequest()->getParam('qid', 0);
//     $questionModel = new Model_Questions();
//     $question = $questionModel->getById($qid);
//     $this->view->question = $question;
//   }
}
