<?php
/**
 * InputController
 * @desc     Beiträge
 * @author        Markus Hackel
 */
class InputController extends Zend_Controller_Action {

  protected $_user = null;
  
  protected $_consultation = null;
  
  protected $_flashMessenger = null;

  /**
   * Construct
   * @see Zend_Controller_Action::init()
   * @return void
   */
  public function init() {
    $kid = $this->getRequest()->getParam('kid', 0);
    $consultationModel = new Model_Consultations();
    $consultation = $consultationModel->find($kid)->current();
    
    $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    
    if ($consultation) {
      $this->_consultation = $consultation;
      $this->view->consultation = $consultation;
    } else {
      $this->_flashMessenger->addMessage('Keine Konsultation angegeben!', 'error');
      $this->_redirect('/');
    }
  }
  /**
   * index
   * @desc Übersicht der Beiträge
   * @return void
   */
  public function indexAction() {
    $kid = $this->_request->getParam('kid', 0);
    $inputModel = new Model_Inputs();
    $questionModel = new Model_Questions();
    $tagModel = new Model_Tags();
    
    $this->view->inputCount = $inputModel->getCountByConsultation($this->_consultation->kid);
    
    $questions = $questionModel->getByConsultation($this->_consultation->kid)->toArray();
    foreach ($questions as $key => $question) {
      $questions[$key]['inputs'] = $inputModel->getByQuestion($question['qi'], null, 4);
    }
    $this->view->questions = $questions;
    
    $this->view->tags = $tagModel->getAllByConsultation($kid);
  }
  
  /**
   * Show single Question with Inputs/Contributions
   *
   */
  public function showAction() {
    $inputModel = new Model_Inputs();
    $questionModel = new Model_Questions();
    $kid = $this->_getParam('kid', 0);
    $qid = $this->_getParam('qid', 0);
    
    $this->view->numberInputs = $inputModel->getCountByQuestion($qid);
    
    $this->view->question = $questionModel->getById($qid);
    
    $form = new Default_Form_Input();
    // falls Formular in save Action nicht validiert werden konnte:
    // -> wurde in Session gespeichert und kann nun hier wiedergeholt werden
    $populateForm = new Zend_Session_Namespace('populateForm');
    if (isset($populateForm->input)) {
      // Klassendefinition sicherstellen
      if (class_exists('Default_Form_Input', true)) {
        // Formular aus Session holen
        $form = unserialize($populateForm->input);
        // Formular in Session löschen
        unset($populateForm->input);
      }
    }
    $form->setAction('/input/save/kid/' . $kid . '/qid/' . $qid);
    $this->view->inputform = $form;
    
    $paginator = Zend_Paginator::factory($inputModel->getSelectByQuestion($qid));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->paginator = $paginator;
  }
  
  public function saveAction() {
    $questionModel = new Model_Questions();
    $inputModel = new Model_Inputs();
    $form = new Default_Form_Input();
    $kid = $this->_getParam('kid', 0);
    $qid = $this->_getParam('qid', 0);
    $redirectURL = '/input/show/kid/' . $kid . '/qid/' . $qid;
    $auth = Zend_Auth::getInstance();
    
    if ($this->_request->isPost()) {
      // Wenn Formular abgeschickt wurde
      $data = $this->_request->getPost();
      if ($form->isValid($data)) {
        $data2store = $form->getValues();
        $data2store['kid'] = $kid;
        $data2store['qi'] = $qid;
        if ($auth->hasIdentity()) {
          $identity = $auth->getIdentity();
          // mit uid speichern
          $data2store['uid'] = $identity->uid;
          $inputModel->add($data2store);
        } else {
          // Beiträge in Session sammeln und nach Registrierung oder Login speichern
          $inputCollection = new Zend_Session_Namespace('inputCollection');
          $tmpCollection = $inputCollection->inputs;
          $tmpCollection[] = $data2store;
          $inputCollection->inputs = $tmpCollection;
        }
        // welche Action als nächstes?
        $nextQuestion = $questionModel->getNext($qid);
        if (!empty($nextQuestion) && $data['submitmode'] == 'save_next') {
          // Gehe zur nächsten Frage
          $this->_flashMessenger->addMessage('Super! Weiter mit der nächsten Frage!', 'success');
          $redirectURL = '/input/show/kid/' . $kid . '/qid/' . $nextQuestion->qi;
        } elseif (empty($nextQuestion) || $data['submitmode'] == 'save_finish') {
          // Gehe zur Bestätigung
          $this->_flashMessenger->addMessage('Ok, also machen wir Schluss für heute!', 'success');
          $redirectURL = '/input/confirm/kid/' . $kid;
        }
      } else {
        // Speichere Formular mit aktuellen Werten in Session um es nach Redirect
        // zur show Action inkl. Fehlermeldungen anzeigen zu können
        $populateForm = new Zend_Session_Namespace('populateForm');
        $populateForm->input = serialize($form->populate($form->getValues()));
        
        $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
      }
    }
    
    $this->redirect($redirectURL);
  }
  
  public function confirmAction() {
    $userModel = new Model_Users();
    $inputModel = new Model_Inputs();
    $kid = $this->_getParam('kid', 0);
    $auth = Zend_Auth::getInstance();
    $inputCollection = new Zend_Session_Namespace('inputCollection');
    
    if ($auth->hasIdentity()) {
      // Nutzer ist eingeloggt
      $identity = $auth->getIdentity();
      if (!empty($inputCollection->inputs)) {
        // falls sich der Nutzer gerade eben erst eingeloggt hat,
        // sind möglicherweise noch Beiträge in der Session,
        // die jetzt in die DB geschrieben werden müssen
        $inputModel->storeSessionInputsInDb($identity->uid);
      }
      // Bestätigungsmail senden
      $sent = $userModel->sendInputsConfirmationMail($identity);
      if ($sent) {
        $this->_flashMessenger->addMessage('Eine E-Mail zur Bestätigung der Beiträge wurde an die hinterlegte Adresse gesendet.', 'success');
      } else {
        $this->_flashMessenger->addMessage('Es gibt keine Beiträge, die noch bestätigt werden müssen.', 'info');
      }
      // auf Startseite weiterleiten
      $this->redirect('/');
    } else {
      // Nutzer nicht eingeloggt
      if (!empty($inputCollection->inputs)) {
        // Beiträge in Session vorhanden
        $loginForm = new Default_Form_Login();
        $this->view->loginForm = $loginForm;
        
        // wenn zum ersten Mal teilgenommen:
        $registerForm = new Default_Form_Register();
        $populateForm = new Zend_Session_Namespace('populateForm');
        if (!empty($populateForm->register)) {
          // vorangegangener Registrierungsversuch fehlerhaft
          if (class_exists('Default_Form_Register', true)) {
            // stelle Formular aus Session wieder her
            $registerForm = unserialize($populateForm->register);
            unset($populateForm->register);
          }
        }
        $registerForm->getElement('kid')->setValue($kid);
        $this->view->registerForm = $registerForm;
      } else {
        // Keine Beiträge in Session
        $this->_flashMessenger->addMessage('Keine Beiträge vorhanden.', 'info');
        $this->redirect('/');
      }
    }
  }
  
  public function mailconfirmAction() {
    $ckey = $this->_getParam('ckey');
    $alnumVal = new Zend_Validate_Alnum();
    $error = false;
    if (!$alnumVal->isValid($ckey)) {
      $error = true;
    } else {
      $inputModel = new Model_Inputs();
      $error = !$inputModel->confirmByCkey($ckey);
    }
    if ($error) {
      $this->_flashMessenger->addMessage('Der eingegebene Bestätigungslink ist ungültig!', 'error');
    }
    $this->redirect('/');
  }
}
