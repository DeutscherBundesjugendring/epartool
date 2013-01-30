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
//              'loginForm' => $form
            ));

//    $data = $con->getVotingResults(12);
//    $this->view->assign(array(
//          'data' => $data
//        ));
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
}
