<?php
/**
 * ArticleController
 * @desc     Artikel zu Konsultationen
 * @author        Markus Hackel
 */
class ArticleController extends Zend_Controller_Action {

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
   * @desc Übersicht der Artikel
   * @return void
   */
  public function indexAction() {
    
  }
  
  /**
   * Show single Article
   *
   */
  public function showAction() {
    $aid = $this->getRequest()->getParam('aid', 0);
    $articleModel = new Model_Articles();
    $article = $articleModel->getById($aid);
    $this->view->article = $article;
  }
}
