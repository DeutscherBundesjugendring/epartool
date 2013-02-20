<?php
/**
 * IndexController
 * @desc     public area
 * @author        Jan Suchandt
 */
class IndexController extends Zend_Controller_Action {

  protected $_auth = null;
  
  protected $_flashMessenger = null;

  /**
   * Construct
   * @see Zend_Controller_Action::init()
   * @return void
   */
  public function init() {
    $this->_auth = Zend_Auth::getInstance();
    $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
  }
  /**
   * home
   * @desc landingpage
   * @return void
   */
  public function indexAction() {
    // get last 3 consultations
    $con = new Model_Consultations();
    $conList = $con->getLast();

    $this->view
        ->assign(
            array(
              'consultations' => $conList,
            ));
  }

  /**
   * Loginform
   * @desc action you reach with login-form
   * @return void
   */
//  public function loginAction() {
//    $form = new Form_Login();
//    $auth = Zend_Auth::getInstance();
//    if ($auth->hasIdentity()) {
//      return $this->_forward('index');
//    }
//  }

  /**
   * Search-Page
   */
  public function searchAction() {
    
    $needle = $this->getRequest()->getParam('q',0);
    // Filter search-needle
    if($needle) {
      // filters
      $filterChain = new Zend_Filter();
      $filterChain->appendFilter(new Zend_Filter_StringTrim());
      $filterChain->appendFilter(new Zend_Filter_StringToLower());
      $filterChain->appendFilter(new Zend_Filter_HtmlEntities());
      // apply filters
      $needle = $filterChain->filter($needle);
      
      // Search in articles with no consultations ("grundinformationen")
      $articles = new Model_Articles();
      $consultation = new Model_Consultations();
      
      $generelResults = $articles->search($needle);
      $consultationResults = $consultation->search($needle);
      
      $this->view->needle = $needle;
      $this->view->resultsGeneral = $generelResults;
      $this->view->resultsConsultations = $consultationResults;
    }
    else {
      // no search-request, redirect
      $this->redirect('');
    }
    
    
    
    // search in articles
    // search in consultation (inputs, tags, consultationtexts)
    
  }
}
