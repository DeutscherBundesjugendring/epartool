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
      $action = $this->_request->getActionName();
      if ($action != 'support') {
        $this->_flashMessenger->addMessage('Keine Konsultation angegeben!', 'error');
        $this->_redirect('/');
      }
    }
    
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('support', 'json')
                ->initContext();
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
      $questions[$key]['inputs'] = $inputModel->getByQuestion($question['qi'], 'tid DESC', 4);
    }
    $this->view->questions = $questions;
    
    $this->view->tags = $tagModel->getAllByConsultation($kid, '', new Zend_Db_Expr('RAND()'));
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
    $tag = $this->_getParam('tag', null);
    $nowDate = Zend_Date::now();
    
    if (empty($qid)) {
      // get first question of this consultation
      $questionRow = $questionModel->getByConsultation($kid)->current();
      $qid = $questionRow->qi;
    }
    
    if (!empty($tag)) {
      $tagModel = new Model_Tags();
      $this->view->tag = $tagModel->getById($tag);
    }
    
    $this->view->numberInputs = $inputModel->getCountByQuestion($qid, $tag);
    
    $this->view->question = $questionModel->getById($qid);
    
    if ($nowDate->isEarlier($this->_consultation->inp_fr)) {
      $form = '<p>Die Beitragsphase hat noch nicht begonnen.</p>';
    } elseif ($nowDate->isLater($this->_consultation->inp_to)) {
      $form = '<p>Die Beitragsphase ist bereits vorbei.</p>';
    } else {
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
      } else {
        $inputCollection = new Zend_Session_Namespace('inputCollection');
        $theses = array();
        if (!empty($inputCollection->inputs)) {
          foreach ($inputCollection->inputs as $input) {
            if ($input['kid'] == $kid && $input['qi'] == $qid) {
              $theses[] = array(
                  'thes' => $input['thes'],
                  'expl' => $input['expl']
              );
            }
          }
        }
        // add form fields for inputs and prefill it with session data
        $form->generate($theses);
      }
      $form->setAction($this->view->baseUrl() . '/input/save/kid/' . $kid . '/qid/' . $qid);
    }
    $this->view->inputform = $form;
    
    $paginator = Zend_Paginator::factory($inputModel->getSelectByQuestion($qid, 'i.tid ASC', null, $tag));
    
    // Determine maximum page number and set it as default value in paginator
    $maxPage = ceil( $paginator->getTotalItemCount() / $paginator->getItemCountPerPage() );
    $paginator->setCurrentPageNumber($this->_getParam('page', $maxPage));
    
    $this->view->paginator = $paginator;
    
  }
  
  /**
   * Saves input in session
   * Redirects to next question or input confirmation page
   * (with login/register form, @see confirmAction())
   *
   */
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
      
      if (isset($data['thes']) && isset($data['expl'])) {
        $data2store = $data;
        
        // Beiträge in Session sammeln
        $inputCollection = new Zend_Session_Namespace('inputCollection');
        if (isset($inputCollection->inputs)) {
          $tmpCollection = $inputCollection->inputs;
          // delete former inputs for this question from session:
          foreach ($tmpCollection as $key => $item) {
            if ($item['qi'] == $qid) {
              unset($tmpCollection[$key]);
            }
          }
          $inputCollection->inputs = $tmpCollection;
        } else {
          $tmpCollection = array();
        }
        
        $i = 0;
        foreach ($data2store['thes'] as $thes) {
          if (!empty($thes)) {
            // Only save Input if form field 'thes' is filled, else simply go to next step
            // Beiträge in Session sammeln
            $tmpCollection[] = array(
                'kid' => $kid,
                'qi' => $qid,
                'thes' => $thes,
                'expl' => $data2store['expl']['expl_' . $i]
            );
            $inputCollection->inputs = $tmpCollection;
          }
          $i++;
        }
        
        // welche Action als nächstes?
        switch ($data['submitmode']) {
          case 'save_plus':
            // show form for current question again
            // jump to form
            $redirectURL.= '/#input';
            break;
            
          case 'save_next':
            $nextQuestion = $questionModel->getNext($qid);
            if (!empty($nextQuestion)) {
              // Gehe zur nächsten Frage
              $redirectURL = '/input/show/kid/' . $kid . '/qid/' . $nextQuestion->qi;
            } else {
              // Gehe zur Bestätigung
              $redirectURL = '/input/confirm/kid/' . $kid;
            }
            break;
            
          case 'save_finish':
            // Gehe zur Bestätigung
            $redirectURL = '/input/confirm/kid/' . $kid;
            break;
            
          case 'save_goto':
            if ($data['goto'] > 0) {
              $redirectURL = '/input/show/kid/' . $kid . '/qid/' . (int)$data['goto'];
            }
        }
        
      } else {
        $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
        $form = new Default_Form_Input();
        $inputCollection = new Zend_Session_Namespace('inputCollection');
        $theses = array();
        if (!empty($inputCollection->inputs)) {
          foreach ($inputCollection->inputs as $input) {
            if ($input['kid'] == $kid && $input['qi'] == $qid) {
              $theses[] = array(
                  'thes' => $input['thes'],
                  'expl' => $input['expl']
              );
            }
          }
        }
        $form->generate($theses);
        $form->setAction($this->view->baseUrl() . '/input/save/kid/' . $kid . '/qid/' . $qid);
        // Speichere Formular mit aktuellen Werten in Session um es nach Redirect
        // zur show Action inkl. Fehlermeldungen anzeigen zu können
        $populateForm = new Zend_Session_Namespace('populateForm');
        $populateForm->input = serialize($form);
        
      }
    }
    
    $this->redirect($redirectURL);
  }
  
  /**
   * Login or register to save inputs into database
   *
   */
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
      $sent = $userModel->sendInputsConfirmationMail($identity, $kid);
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
//         $loginForm = new Default_Form_Login();
//         $this->view->loginForm = $loginForm;
        
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
  
  /**
   * Process input confirmation from email link
   *
   */
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
  
  /**
   * Process input confirmation from email link
   * reject inputs in this case
   *
   */
  public function mailrejectAction() {
    $ckey = $this->_getParam('ckey');
    $alnumVal = new Zend_Validate_Alnum();
    $error = false;
    if (!$alnumVal->isValid($ckey)) {
      $error = true;
    } else {
      $inputModel = new Model_Inputs();
      $error = !$inputModel->confirmByCkey($ckey, true);
    }
    if ($error) {
      $this->_flashMessenger->addMessage('Der eingegebene Bestätigungslink ist ungültig!', 'error');
    }
    $this->redirect('/');
  }
  
  /**
   * Called by ajax request, switches context to json
   *
   */
  public function supportAction() {
    $data = $this->_request->getPost();
    if (empty($data['tid'])) {
      $this->redirect('/');
    }
    $supports = new Zend_Session_Namespace('supports');
    if (empty($supports->clicks)) {
      $supports->clicks = array();
    }
    $inputsModel = new Model_Inputs();
    if (!in_array($data['tid'], $supports->clicks)) {
      $this->view->count = $inputsModel->addSupport($data['tid']);
      $supports->clicks[] = $data['tid'];
    }
  }
  
  /**
   * Edit user inputs
   *
   */
  public function editAction() {
    $kid = $this->_request->getParam('kid', 0);
    $tid = $this->_request->getParam('tid', 0);
    $validator = new Zend_Validate_Int();
    
    // parameter validation
    $error = false;
    if (!$validator->isValid($kid)) {
      $error = true;
    }
    if (!$validator->isValid($tid)) {
      $error = true;
    }
    $inputsModel = new Model_Inputs();
    $input = $inputsModel->getById($tid);
    if (empty($input) || $input['kid'] != $kid) {
      $error = true;
    }
    if ($error) {
      $this->_flashMessenger->addMessage('Seite nicht gefunden!', 'error');
      $this->redirect('/');
    }
    if (Zend_Date::now()->isEarlier($this->_consultation->inp_to)) {
      // allow editing only BEFORE inputs period is over
      $form = new Default_Form_Input_Edit();
      if ($this->_request->isPost()) {
        // form submitted
        $data = $this->_request->getPost();
        if ($form->isValid($data)) {
          $key = $inputsModel->updateById($tid, $data);
          if ($key > 0) {
            $this->_flashMessenger->addMessage('Beitrag aktualisiert.', 'success');
          } else {
            $this->_flashMessenger->addMessage('Etwas lief schief: Beitrag konnte nicht aktualisiert werden.', 'error');
          }
          $this->redirect($this->view->url(array(
            'controller' => 'user',
            'action' => 'inputlist',
            'kid' => $kid
          )), array('prependBase' => false));
        } else {
          $this->_flashMessenger->addMessage('Bitte prüfe Deine Eingaben!', 'error');
          $form->populate($data);
        }
      } else {
        // form not submitted, show original data
        $form->getElement('thes')->setValue($input['thes']);
        $form->getElement('expl')->setValue($input['expl']);
      }
      $this->view->form = $form;
    } else {
      // inputs period is already over
      $this->view->message = 'Die Beitragszeit für diese Konsultation ist leider vorbei.'
        . ' Beiträge können nur innerhalb der Beitragszeit geändert werden.';
    }
  }
  
  public function tagsAction() {
    $kid = $this->_request->getParam('kid', 0);
    $inputModel = new Model_Inputs();
    $tagModel = new Model_Tags();
    
    $this->view->inputCount = $inputModel->getCountByConsultation($this->_consultation->kid);
    
    $this->view->tags = $tagModel->getAllByConsultation($kid);
  }
  
}
