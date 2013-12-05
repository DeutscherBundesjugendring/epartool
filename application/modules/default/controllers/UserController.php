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
    $this->redirect('/');
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
      $raw_data = $this->_request->getPost();

      if (!$this->_auth->hasIdentity()) {
        // if not already logged in

        // check if email is valid
        // if it exists already: store inputs
        // for the already registered user with new user info data
        $emailValidator = new Zend_Validate_EmailAddress();
        if (!$emailValidator->isValid($raw_data['email'])) {
          $populateForm = new Zend_Session_Namespace('populateForm');
          $form->populate($raw_data);
          $populateForm->register = serialize($form);
          $this->_flashMessenger->addMessage('Bitte prüfe Deine Eingaben!', 'error');
          $this->redirect('/input/confirm/kid/' . $form->getValue('kid'));
        }

        $userModel = new Model_Users();

        if ($form->isValid($raw_data)) {
          $data = $form->getValues();
          if ($data['group_type'] != 'group') {
            unset($data['group_specs']);
          }

          $registerInfo = $userModel->register($data);
          
          $userModel->sendInputsConfirmationMail($registerInfo['uid'], $form->getValue('kid'), $registerInfo['password']);
          $this->_flashMessenger
            ->addMessage('Eine Mail zur Bestätigung der Beiträge wurde an die angegebene E-Mail-Adresse gesendet.', 'success');

          $this->redirect('/');

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

  /**
   * @deprecated User Registration will now be implicitly confirmed when inputs are confirmed
   *
   */
  public function registerconfirmAction() {
    $ckey = $this->_request->getParam('ckey');
    $kid = $this->_request->getParam('kid');
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
        $userModel->sendInputsConfirmationMail($identity, $kid);
      }
    }
    if ($error) {
      $this->_flashMessenger->addMessage('Der eingegebene Bestätigungslink ist ungültig!', 'error');
    }
    $this->redirect('/');
  }

  public function editAction() {

    $this->_flashMessenger->addMessage('Noch nicht implementiert!', 'info');
    $this->redirect('/');
  }

  public function inputlistAction() {
    $kid = $this->_request->getParam('kid', 0);
    $consultationModel = new Model_Consultations();
    if ($this->_auth->hasIdentity()) {
      $identity = $this->_auth->getIdentity();
      if ($kid == 0) {
        $this->view->consultationList = $consultationModel->getByUser($identity->uid);
      } elseif ($kid > 0) {
        $this->view->consultation = $consultationModel->find($kid)->current();
        $inputModel = new Model_Inputs();
        $this->view->inputs = $inputModel->getUserEntriesOverview($identity->uid, $kid);
      }
    } else {
      $this->_flashMessenger->addMessage('Bitte erst anmelden!', 'error');
    }
  }

  public function passwordrecoverAction() {
    $form = new Default_Form_PasswordRecover();
    if ($this->_request->isPost()) {
      $data = $this->_request->getPost();
      if ($form->isValid($data)) {
        $userModel = new Model_Users();
        if ($userModel->recoverPassword($data['email'])) {
          $this->_flashMessenger->addMessage('Eine E-Mail mit einem neuen Passwort wurde an die angegebene E-Mail-Adresse verschickt!', 'success');
        } else {
          $this->_flashMessenger->addMessage('Passwortwiederherstellung fehlgeschlagen!', 'error');
        }
        $this->redirect('/');
      } else {
        $this->_flashMessenger->addMessage('Bitte prüfe deine Eingaben!', 'error');
      }
    }
    $this->view->form = $form;
  }
}
