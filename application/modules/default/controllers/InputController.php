<?php
/**
 * InputController
 * @desc     Beiträge
 * @author        Markus Hackel
 */
class InputController extends Zend_Controller_Action {

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
   * @desc Übersicht der Beiträge
   * @return void
   */
  public function indexAction() {
    $inputModel = new Model_Inputs();
    $questionModel = new Model_Questions();
    $tagModel = new Model_Tags();
    
    $this->view->inputCount = $inputModel->getCountByConsultation($this->_consultation->kid);
    
    $questions = $questionModel->getByConsultation($this->_consultation->kid)->toArray();
    foreach ($questions as $key => $question) {
      $questions[$key]['inputs'] = $inputModel->getByQuestion($question['qi'], null, 4);
    }
    $this->view->questions = $questions;
    
//    $this->view->tags = $tagModel->
  }
  
  /**
   * Show single Question with Inputs/Contributions
   *
   */
  public function showAction() {
    $inputModel = new Model_Inputs();
    $questionModel = new Model_Questions();
    $qid = $this->_getParam('qid', 0);
    
    $this->view->numberInputs = $inputModel->getCountByQuestion($qid);
    
    $this->view->question = $questionModel->getById($qid);
    
    $paginator = Zend_Paginator::factory($inputModel->getSelectByQuestion($qid));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->paginator = $paginator;
  }
}
