<?php
/**
 * VotingController
 * @desc     Abstimmung
 * @author        Markus Hackel
 */
class VotingController extends Zend_Controller_Action {

  protected $_user = null;

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
   * @desc Startseite Abstimmung
   * @return void
   */
  public function indexAction() {
    
  }
}
