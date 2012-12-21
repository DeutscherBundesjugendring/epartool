<?php
/**
 * IndexController
 * @desc     public area
 * @author        Jan Suchandt
 */
class IndexController extends Zend_Controller_Action {

  protected $_user = null;

  /**
   * Construct
   * @see Zend_Controller_Action::init()
   * @return void
   */
  public function init() {
    $usr = new Users();

  }
  /**
   * home
   * @desc landingpage
   * @return void
   */
  public function indexAction() {
    // init formloader
    $formClass = Zend_Registry::get('formloader')->load('Login');
    // Formular-Klasse erstellen
    $form = new $formClass();

    // get last 3 consultations
    $con = new Consultations();
    $conList = $con->getLast();

    $this->view
        ->assign(
            array(
              'consultations' => $conList, 'loginForm' => $form
            ));

    $data = $con->getVotingResults(12);
    $this->view->assign(array(
          'data' => $data
        ));
  }

  /**
   * Loginform
   * @desc action you reach with login-form
   * @return void
   */
  public function loginAction() {}
}
