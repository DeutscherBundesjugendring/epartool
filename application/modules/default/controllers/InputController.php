<?php
/**
 * InputController
 * @desc         Beiträge
 * @author                Markus Hackel
 */
class InputController extends Zend_Controller_Action
{
    protected $_user = null;

    protected $_consultation = null;

    protected $_flashMessenger = null;

    protected $_inputform = null;

    /**
     * Construct
     * @see Zend_Controller_Action::init()
     * @return void
     */
    public function init()
    {
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
                $this->_flashMessenger->addMessage('Keine Beteiligungsrunde angegeben!', 'error');
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
    public function indexAction()
    {
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
        $this->view->nowDate = Zend_Date::now();

        $this->view->tags = $tagModel->getAllByConsultation($kid, '', new Zend_Db_Expr('RAND()'));
    }

    /**
     * Show single Question with Inputs/Contributions
     *
     */
    public function showAction()
    {
        $inputModel = new Model_Inputs();
        $questionModel = new Model_Questions();
        $kid = $this->_getParam('kid', 0);
        $qid = $this->_getParam('qid', 0);
        $tag = $this->_getParam('tag', null);
        $nowDate = Zend_Date::now();

        if ($this->getRequest()->isPost()) {
            $this->_handleInputRequest();
        }

        if (empty($qid)) {
            $questionRow = $questionModel->getByConsultation($kid)->current();
            $qid = $questionRow->qi;
        }

        if (!empty($tag)) {
            $tagModel = new Model_Tags();
            $this->view->tag = $tagModel->getById($tag);
        }

        if ($nowDate->isEarlier($this->_consultation->inp_fr)) {
            $form = '<p>Die Beitragsphase hat noch nicht begonnen.</p>';
        } elseif ($nowDate->isLater($this->_consultation->inp_to)) {
            $form = '<p>Die Beitragsphase ist bereits vorbei.</p>';
        } else {
            $form = $this->_getInputform();
            $sessInputs = new Zend_Session_Namespace('inputs');
            $theses = [];
            if (!empty($sessInputs->inputs)) {
                foreach ($sessInputs->inputs as $input) {
                    if ($input['qi'] == $qid) {
                        $theses[] = [
                            'thes' => $input['thes'],
                            'expl' => $input['expl']
                        ];
                    }
                }
            }
            $form->generateInputFields($theses);
            $form->setAction($this->view->baseUrl() . '/input/show/kid/' . $kid . '/qid/' . $qid);
        }

        $paginator = Zend_Paginator::factory($inputModel->getSelectByQuestion($qid, 'i.tid ASC', null, $tag));

        // Determine maximum page number and set it as default value in paginator
        $maxPage = ceil($paginator->getTotalItemCount() / $paginator->getItemCountPerPage());
        $paginator->setCurrentPageNumber($this->_getParam('page', $maxPage));

        $this->view->paginator = $paginator;
        $this->view->inputform = $form;
        $this->view->numberInputs = $inputModel->getCountByQuestion($qid, $tag);
        $this->view->question = $questionModel->getById($qid);

    }

    /**
     * Saves input in session, called in showAction() if form submitted.
     * Redirects to next question or input confirmation page if form is valid
     */
    protected function _handleInputRequest()
    {
        $questionModel = new Model_Questions();
        $form = $this->_getInputform();
        $kid = $this->_getParam('kid', 0);
        $qid = $this->_getParam('qid', 0);
        $redirectURL = '/input/show/kid/' . $kid . '/qid/' . $qid;

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $values = $this->_request->getPost();

                $sessInputs = (new Zend_Session_Namespace('inputs'));
                if (isset($sessInputs->inputs)) {
                    $tmpCollection = $sessInputs->inputs;
                    // delete former inputs for this question from session:
                    foreach ($tmpCollection as $key => $item) {
                        if ($item['qi'] == $qid) {
                            unset($tmpCollection[$key]);
                        }
                    }
                    $sessInputs->inputs = $tmpCollection;
                } else {
                    $tmpCollection = array();
                }

                foreach ($values['inputs'] as $input) {
                    if (!empty($input['thes'])) {
                        $tmpCollection[] = array(
                                'kid' => $kid,
                                'qi' => $qid,
                                'thes' => $input['thes'],
                                'expl' => $input['expl']
                        );
                        $sessInputs->inputs = $tmpCollection;
                    }
                }

                if (isset($values['add_input_field'])) {
                    $redirectURL.= '/#input';
                } elseif (isset($values['next_question'])) {
                    $nextQuestion = $questionModel->getNext($qid);
                    $redirectURL = '/input/show/kid/' . $kid . ($nextQuestion ? '/qid/' . $nextQuestion->qi : '');
                } elseif (isset($values['finished'])) {
                    $redirectURL = '/input/confirm/kid/' . $kid;
                }
                $this->redirect($redirectURL);
            } else {
                $this->_flashMessenger->addMessage(
                    'Bitte prüfe Deine Eingaben! Es könnte auch sein, dass du die maximale Bearbeitungszeit von '
                    . number_format(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl / 60, 0)
                    . ' Minuten überschritten hast.',
                    'error'
                );
            }
        }

    }

    /**
     * Login or register to save inputs into database
     */
    public function confirmAction()
    {
        $kid = $this->_getParam('kid', 0);
        $auth = Zend_Auth::getInstance();
        $sessInputs = new Zend_Session_Namespace('inputs');
        $regFormData = new Zend_Session_Namespace('populateForm');

        if (!empty($sessInputs->inputs)) {
            // After entring inputs
            $inputModel = new Model_Inputs();
            $confirmKey = $inputModel->getConfirmationKey();
            try {
                $inputModel->getAdapter()->beginTransaction();
                foreach ($sessInputs->inputs as $input) {
                    $input['uid'] = $auth->hasIdentity() ? $auth->getIdentity()->uid : null;
                    $input['confirmation_key'] = $auth->hasIdentity() ? null : $confirmKey;
                    $input['user_conf'] = $auth->hasIdentity() ? 'c' : 'u';
                    $inputModel->add($input);
                }
                $inputModel->getAdapter()->commit();
                unset($sessInputs->inputs);
            } catch (Exception $e) {
                $inputModel->getAdapter()->rollback();
                throw $e;
            }

            if ($auth->hasIdentity()) {
                $this->_flashMessenger->addMessage(
                    'Your inputs have been saved.',
                    'success'
                );
                $this->redirect('/');
            } else {
                $sessInputs->confirmKey = $confirmKey;
                $registerForm = new Default_Form_Register();
                $registerForm->getElement('kid')->setValue($kid);
                $this->view->registerForm = $registerForm;
            }
        } elseif ($regFormData->register) {
            // If submited registration form was invalid, the redirect from UserController::register()
            $registerForm = unserialize($regFormData->register);
            unset($regFormData->register);
            $this->view->registerForm = $registerForm;
        } else {
            $this->_flashMessenger->addMessage(
                'Es gibt keine Beiträge, die noch bestätigt werden müssen.',
                'info'
            );
            $this->redirect('/');
        }
    }

    /**
     * Process input confirmation from email link - confirm inputs
     */
    public function mailconfirmAction()
    {
        $ckey = $this->_getParam('ckey');
        $inputModel = new Model_Inputs();
        $inputModel->getAdapter()->beginTransaction();
        try {
            $confirmedCount = $inputModel->confirmByCkey($ckey);
            $inputModel->getAdapter()->commit();
        } catch (Dbjr_UrlkeyAction_Exception $e){
            $inputModel->getAdapter()->rollback();
            $this->_flashMessenger->addMessage('It is not allowed to confirm inputs once the input phase is over.', 'error');
            $this->redirect('/');
        } catch (Exception $e) {
            $inputModel->getAdapter()->rollback();
            throw $e;
        }

        if ($confirmedCount) {
            $this->_flashMessenger->addMessage('Vielen Dank! Deine Beiträge wurden bestätigt!', 'success');
        } else {
            $this->_flashMessenger->addMessage('Der eingegebene Bestätigungslink ist ungültig!', 'error');
        }
        $this->redirect('/');
    }

    /**
     * Process input confirmation from email link - reject inputs
     */
    public function mailrejectAction()
    {
        $ckey = $this->_getParam('ckey');
        $inputModel = new Model_Inputs();
        $inputModel->getAdapter()->beginTransaction();
        try {
            $rejectedCount = $inputModel->rejectByCkey($ckey);
            $inputModel->getAdapter()->commit();
        } catch (Dbjr_UrlkeyAction_Exception $e){
            $inputModel->getAdapter()->rollback();
            $this->_flashMessenger->addMessage('It is not allowed to reject inputs once the input phase is over.', 'error');
            $this->redirect('/');
        }
         catch (Exception $e) {
            $inputModel->getAdapter()->rollback();
            throw $e;
        }

        if ($rejectedCount) {
            $this->_flashMessenger->addMessage('Die Beiträge wurden als abgelehnt markiert!', 'success');
        } else {
            $this->_flashMessenger->addMessage('Der eingegebene Bestätigungslink ist ungültig!', 'error');
        }
        $this->redirect('/');
    }

    /**
     * Called by ajax request, switches context to json
     */
    public function supportAction()
    {
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
     */
    public function editAction()
    {
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
        if (empty($input)) {
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
                        $this->_flashMessenger->addMessage(
                            'Etwas lief schief: Beitrag konnte nicht aktualisiert werden.',
                            'error'
                        );
                    }
                    $this->redirect(
                        $this->view->url(
                            array(
                                'controller' => 'user',
                                'action' => 'inputlist',
                                'kid' => $kid
                            )
                        ),
                        array('prependBase' => false)
                    );
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
            $this->view->message = 'Die Beitragszeit für diese Beteiligungsrunde ist leider vorbei.'
                . ' Beiträge können nur innerhalb der Beitragszeit geändert werden.';
        }
    }

    public function tagsAction()
    {
        $kid = $this->_request->getParam('kid', 0);
        $inputModel = new Model_Inputs();
        $tagModel = new Model_Tags();

        $this->view->inputCount = $inputModel->getCountByConsultation($this->_consultation->kid);

        $this->view->tags = $tagModel->getAllByConsultation($kid);
    }

    protected function _getInputform()
    {
        if (null === $this->_inputform) {
         $this->_inputform = new Default_Form_Input_Create();
        }

        return $this->_inputform;
    }
}
