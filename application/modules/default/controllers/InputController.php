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
        $ajaxContext
            ->addActionContext('support', 'json')
            ->initContext();
    }

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
     * Show single Question with Inputs
     */
    public function showAction()
    {
        $inputModel = new Model_Inputs();
        $questionModel = new Model_Questions();
        $kid = $this->_getParam('kid', 0);
        $qid = $this->_getParam('qid', 0);
        $tag = $this->_getParam('tag', null);
        $auth = Zend_Auth::getInstance();

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if (isset($post['subscribe'])) {
                $sbsForm = $this->_handleSubscribeQuestion($post, $kid, $qid, $auth);
            } elseif (isset($post['unsubscribe']) && $auth->hasIdentity()) {
                $this->_handleUnsubscribeQuestion($post, $kid, $qid, $auth);
            } else {
                $this->_handleInputSubmit($post, $kid, $qid);
            }
        }

        if (empty($qid)) {
            $questionRow = $questionModel->getByConsultation($kid)->current();
            $qid = $questionRow->qi;
        }

        if (!empty($tag)) {
            $tagModel = new Model_Tags();
            $this->view->tag = $tagModel->getById($tag);
        }

        if (Zend_Date::now()->isLater($this->_consultation->inp_fr) && Zend_Date::now()->isEarlier($this->_consultation->inp_to)) {
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
        $maxPage = ceil($paginator->getTotalItemCount() / $paginator->getItemCountPerPage());
        $paginator->setCurrentPageNumber($this->_getParam('page', $maxPage));

        $this->view->subscriptionForm = isset($sbsForm) ? $sbsForm : $this->_getSubscriptionForm($qid);
        $this->view->paginator = $paginator;
        $this->view->inputForm = isset($form) ? $form : null;
        $this->view->numberInputs = $inputModel->getCountByQuestion($qid, $tag);
        $this->view->question = $questionModel->getById($qid);

    }

    /**
     * Handles request to subscribe user to recieve notifications of new inputs belonging to this question
     * @param $post  The data received in post request
     * @param $kid   The consultation identifier
     * @param $qid   The qiestion identifier
     * @param $auth  The auth adapter
     */
    private function _handleSubscribeQuestion($post, $kid, $qid, $auth)
    {
        if ($auth->hasIdentity()) {
            Zend_Registry::get('dbAdapter')->beginTransaction();
            try {
                $userId = $auth->getIdentity()->uid;
                (new Service_Notification_Input_Created())->subscribeUser(
                    $userId,
                    [Service_Notification_Input_Created::PARAM_QUESTION_ID => $qid],
                    Service_Notification_Input_Created::POSTSUBSCRIBE_ACTION_CONFIRM_IMMEDIATE
                );
                Zend_Registry::get('dbAdapter')->commit();
                $this->_flashMessenger->addMessage('Thank you for subscribing.', 'success');
                $this->_redirect('/input/show/kid/' . $kid . '/qid/' . $qid);
            } catch (Exception $e) {
                Zend_Registry::get('dbAdapter')->rollback();
                throw $e;
            }
        } else {
            $sbsForm = new Default_Form_Input_SubscriptionQuestion();
            $sbsForm->addEmailField();
            if (isset($post['email'])) {
                if ($sbsForm->isValid($post)) {
                    Zend_Registry::get('dbAdapter')->beginTransaction();
                    try {
                        list($userId, $isNew) = (new Model_Users())->register($sbsForm->getValues());
                        (new Service_Notification_Input_Created())->subscribeUser(
                            $userId,
                            [Service_Notification_Input_Created::PARAM_QUESTION_ID => $qid],
                            Service_Notification_Input_Created::POSTSUBSCRIBE_ACTION_CONFIRM_EMAIL_REQUEST
                        );
                        Zend_Registry::get('dbAdapter')->commit();
                        $this->_flashMessenger->addMessage('You are now subscribed. A confirmation email has been send.', 'success');
                        $this->_redirect('/input/show/kid/' . $kid . '/qid/' . $qid);
                    } catch (Dbjr_Notification_Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        $this->_flashMessenger->addMessage('You are now subscribed.', 'success');
                        $this->_redirect('/input/show/kid/' . $kid . '/qid/' . $qid);
                    } catch (Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        throw $e;
                    }
                } else {
                    $this->_flashMessenger->addMessage('Subscription form is invalid.', 'error');
                }
            }
        }

        return isset($sbsForm) ? $sbsForm : null;
    }

    /**
     * Handles request to unsubscribe user from recieving notifications of new inputs belonging to this question
     * @param $post  The data received in post request
     * @param $kid   The consultation identifier
     * @param $qid   The qiestion identifier
     * @param $auth  The auth adapter
     */
    private function _handleUnsubscribeQuestion($post, $kid, $qid, $auth)
    {
        $unsbsForm = new Default_Form_Input_UnsubscriptionQuestion();
        if ($unsbsForm->isValid($post)) {
            $userId = $auth->getIdentity()->uid;
            (new Service_Notification_Input_Created())->unsubscribeUser(
                $userId,
                [Service_Notification_Input_Created::PARAM_QUESTION_ID => $qid]
            );
            $this->_flashMessenger->addMessage('You have been unsubscribed.', 'success');
            $this->_redirect('/input/show/kid/' . $kid . '/qid/' . $qid);
        }
    }

    /**
     * Saves input in session, called in showAction() if form submitted.
     * If form is valid, it redirects based on the submit button pressed
     * - next question
     * - input confirmation page
     * - same page with field for extra input
     * @param $post  The data received in post request
     * @param $kid   The consultation identifier
     * @param $qid   The qiestion identifier
     */
    private function _handleInputSubmit($post, $kid, $qid)
    {
        $redirectURL = '/input/show/kid/' . $kid . '/qid/' . $qid;

        $form = $this
            ->_getInputform()
            ->generateInputFields($post['inputs']);

        if ($form->isValid($this->_request->getPost())) {
            $values = $form->getValues();

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

            foreach ($post['inputs'] as $input) {
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

            if (isset($post['add_input_field'])) {
                $redirectURL.= '/#input';
            } elseif (isset($post['next_question'])) {
                $nextQuestion = (new Model_Questions())->getNext($qid);
                $redirectURL = '/input/show/kid/' . $kid . ($nextQuestion ? '/qid/' . $nextQuestion->qi : '');
            } elseif (isset($post['finished'])) {
                $redirectURL = '/input/confirm/kid/' . $kid;
            }
            $this->redirect($redirectURL);
        } else {
            $this->_flashMessenger->addMessage(
                sprintf(
                    'Bitte prüfe Deine Eingaben! Es könnte auch sein, dass du die maximale Bearbeitungszeit von %s  Minuten überschritten hast.',
                    number_format(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl / 60, 0)
                ),
                'error'
            );
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

            $sessInputs->confirmKey = $confirmKey;
            $registerForm = new Default_Form_Register();
            $registerForm->getElement('kid')->setValue($kid);
            if ($auth->hasIdentity()) {
                $user = (new Model_Users())->fetchRow(
                    (new Model_Users())
                        ->select()
                        ->where('email=?', $auth->getIdentity()->email)
                )->toArray();
                $registerForm->populate($user);
                $registerForm->lockEmailField();
            }
            $this->view->registerForm = $registerForm;
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

    /**
     * Return question subscription form based on weather user is:
     * - logged in and subscribed
     * - logged in and unsubscribed
     * - not loggedin
     * @param integer $qid  The question identifier
     * @return Zend_Form    The form object
     */
    private function _getSubscriptionForm($qid)
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()
            && (new Service_Notification_Input_Created())->isSubscribed(
                $auth->getIdentity()->uid,
                [Service_Notification_Input_Created::PARAM_QUESTION_ID => $qid]
            )
        ) {
            $form = new Default_Form_Input_UnsubscriptionQuestion();
        } else {
            $form = new Default_Form_Input_SubscriptionQuestion();
            if (!$auth->hasIdentity()) {
                $form->addEmailField();
            }
        }

        return $form;
    }
}
