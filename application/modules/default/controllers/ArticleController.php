<?php
/**
 * ArticleController
 * @desc     Artikel zu Konsultationen
 * @author        Markus Hackel
 */
class ArticleController extends Zend_Controller_Action {

  protected $_flashMessenger = null;
  
  protected $_consultation = null;
  
  protected $_staticPage = null;

  /**
   * Construct
   * @see Zend_Controller_Action::init()
   * @return void
   */
  public function init() {
    $this->_flashMessenger = $this->getHelper('flashMessenger');
    $kid = $this->getRequest()->getParam('kid', 0);
    // Param 'ref' added through static route definition if applicable
    $ref = $this->getRequest()->getParam('ref');
//    $route = $this->getFrontController()->getRouter()->getCurrentRouteName();
    if ($kid > 0) {
      $consultationModel = new Model_Consultations();
      $consultation = $consultationModel->find($kid)->current();
      if ($consultation) {
        $this->_consultation = $consultation;
        $this->view->consultation = $consultation;
      } else {
        $this->redirect('/');
      }
    } elseif (!empty($ref)) {
      $this->_staticPage = $ref;
    } else {
      // general info page (ref_nm 'static')
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
    if ($aid > 0) {
      $article = $articleModel->getById($aid);
    } elseif (!empty($this->_staticPage)) {
      $article = $articleModel->getByRefName($this->_staticPage);
    }
    if ($article) {
      $this->view->article = $article;
    } else {
      $this->_flashMessenger->addMessage('Seite nicht gefunden!', 'error');
      $this->redirect('/');
    }
  }
}
