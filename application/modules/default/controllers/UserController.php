<?php
/**
 * UserController
 *
 */
class UserController extends Zend_Controller_Action {
  
  protected $_auth = null;
  
  protected $_flashMessenger = null;
  
  public function init() {
    $this->_auth = Zend_Auth::getInstance();
    $this->_flashMessenger = $this->_helper->getHelper('flashMessenger');
  }
  /**
   * Dashboard
   *
   * @return void
   */
  public function indexAction() {}

  /**
   * Stammdaten
   *
   * @return void
   */
  public function accountAction() {}

  /**
   * Anmeldung
   *
   * @return void
   */
  public function loginAction() {}

  /**
   * Abmeldung
   *
   * @return void
   */
  public function logoutAction() {
    Zend_Auth::getInstance()->clearIdentity();
    $this->_flashMessenger->addMessage('Logout erfolgreich!', 'info');
    $this->_redirect('/');
  }

  /**
   * Register
   *
   * @return void
   */
  public function registerAction() {
    if (!$this->_request->isPost()) {
      // no form sent
      $this->redirect('/');
    } else {
      $form = new Default_Form_Register();
      if (!$this->_auth->hasIdentity()) {
        // if not already logged in
        if ($form->isValid($this->_request->getPost())) {
          $userModel = new Model_Users();
          $data = $form->getValues();
          if ($data['group_type'] != 'group') {
            unset($data['group_specs']);
          }
          if ($userModel->register($data)) {
            // register confirmation requested
            $this->_flashMessenger
              ->addMessage('Eine Mail zur Bestätigung der Registrierung wurde an die angegebene E-Mail-Adresse gesendet.'
                . '<br/>Nach Bestätigung der Registrierung wird eine weitere E-Mail zur Bestätigung der Beiträge verschickt werden.', 'success');
            $this->redirect('/');
          } else {
            $populateForm = new Zend_Session_Namespace('populateForm');
            $populateForm->register = serialize($form);
            $this->redirect('/input/confirm/kid/' . $form->getValue('kid'));
          }
        } else {
          $populateForm = new Zend_Session_Namespace('populateForm');
          $populateForm->register = serialize($form);
          $this->_flashMessenger->addMessage('Bitte prüfe Deine Eingaben!', 'error');
          $this->redirect('/input/confirm/kid/' . $form->getValue('kid'));
        }
      } else {
        // user already logged in
        $this->_flashMessenger->addMessage('Du bist bereits eingeloggt!', 'info');
        $this->redirect('/');
      }
    }
  }
  
  public function registerconfirmAction() {
    $ckey = $this->_getParam('ckey');
    $alnumVal = new Zend_Validate_Alnum();
    $error = false;
    if (!$alnumVal->isValid($ckey)) {
      $error = true;
    } else {
      $userModel = new Model_Users();
      // Registrierung bestätigen
      // und Nutzer einloggen
      $error = !$userModel->confirmByCkey($ckey);
      if (!$error) {
        // Bestätigungsmail für Beiträge senden
        $identity = $this->_auth->getIdentity();
        $userModel->sendInputsConfirmationMail($identity);
      }
    }
    if ($error) {
      $this->_flashMessenger->addMessage('Der eingegebene Bestätigungslink ist ungültig!', 'error');
    }
    $this->redirect('/');
  }
}
