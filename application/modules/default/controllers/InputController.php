<?php

class InputController extends Zend_Controller_Action
{

    /**
     * @var Zend_Db_Table_Row_Abstract
     */
    private $consultation;

    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    private $flashMessenger;

    /**
     * @var Default_Form_Input_Create
     */
    private $inputForm = null;


    public function init()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $consultationModel = new Model_Consultations();
        $consultation = $consultationModel->find($kid)->current();

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');

        if ($consultation) {
            $this->consultation = $consultation;
            $this->view->consultation = $consultation;
        } else {
            $action = $this->_request->getActionName();
            if ($action != 'support') {
                $this->flashMessenger->addMessage('No consultation provided!', 'error');
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
        $questions = (new Model_Questions())->getByConsultation($this->consultation->kid)->toArray();
        foreach ($questions as $key => $question) {
            $questions[$key]['inputs'] = $inputModel->getByQuestion($question['qi'], 'tid DESC', 4);
        }

        $this->view->questions = $questions;
        $this->view->nowDate = Zend_Date::now();
        $this->view->inputCount = $inputModel->getCountByConsultation($this->consultation->kid);
        $this->view->tags = (new Model_Tags())->getAllByConsultation($kid, '', new Zend_Db_Expr('RAND()'));
    }

    /**
     * Show single Question with Inputs
     */
    public function showAction()
    {
        $inputModel = new Model_Inputs();
        $questionModel = new Model_Questions();
        $kid = $this->_getParam('kid', 0);
        $qid = $this->_getParam('qid', $questionModel->getByConsultation($kid)->current()->qi);
        $tag = $this->_getParam('tag');
        $auth = Zend_Auth::getInstance();
        $sbsForm = (new Service_Notification_SubscriptionFormFactory())->getForm(
            new Service_Notification_InputCreatedNotification(),
            [Service_Notification_InputCreatedNotification::PARAM_QUESTION_ID => $qid]
        );

        $form = $this->getInputForm();
        $question = (new Model_Questions())->find($qid)->current();
        $form->setVideoEnabled($question['video_enabled']);
        $this->view->videoEnabled = $question['video_enabled'];

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if (isset($post['subscribe'])) {
                $this->handleSubscribeQuestion($post, $kid, $qid, $auth, $sbsForm);
            } elseif (isset($post['unsubscribe']) && $auth->hasIdentity()) {
                $this->handleUnsubscribeQuestion($post, $kid, $qid, $auth, $sbsForm);
            } elseif (isset($post['add_input_field']) || isset($post['finished'])
                || isset($post['next_question'])) {
                $this->handleInputSubmit($post, $kid, $qid);
            }
        }

        if (Zend_Date::now()->isLater(new Zend_Date($this->consultation->inp_fr, Zend_Date::ISO_8601))
            && Zend_Date::now()->isEarlier(new Zend_Date($this->consultation->inp_to, Zend_Date::ISO_8601))
        ) {
            $form = $this->getInputForm();
            $sessInputs = new Zend_Session_Namespace('inputs');
            $theses = [];
            if (!empty($sessInputs->inputs)) {
                foreach ($sessInputs->inputs as $input) {
                    if ($input['qi'] == $qid) {
                        $theses[] = [
                            'thes' => $input['thes'],
                            'expl' => $input['expl'],
                            'video_service' => $input['video_service'],
                            'video_id' => $input['video_id'],
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

        $project = (new Model_Projects())->find((new Zend_Registry())->get('systemconfig')->project)->current();
        $this->view->videoServicesStatus = $project;
        $this->view->subscriptionForm = $sbsForm;
        $this->view->tag = $tag ? (new Model_Tags())->getById($tag) : null;
        $this->view->paginator = $paginator;
        $this->view->inputForm = isset($form) ? $form : null;
        $this->view->numberInputs = $inputModel->getCountByQuestion($qid, $tag);
        $this->view->question = $questionModel->getById($qid);
    }

    /**
     * Handles request to subscribe user to recieve notifications of new inputs belonging to this question
     * @param array $post The data received in post request
     * @param int $kid The consultation identifier
     * @param int $qid The qiestion identifier
     * @param \Zend_Auth $auth The auth adapter
     * @param \Default_Form_SubscribeNotification $sbsForm
     * @return \Default_Form_SubscribeNotification|null
     * @throws \Exception
     * @throws \Zend_Exception
     * @throws \Zend_Form_Exception
     */
    private function handleSubscribeQuestion(
        array $post,
        $kid,
        $qid,
        Zend_Auth $auth,
        Default_Form_SubscribeNotification $sbsForm
    ) {
        if ($auth->hasIdentity()) {
            Zend_Registry::get('dbAdapter')->beginTransaction();
            try {
                $userId = $auth->getIdentity()->uid;
                (new Service_Notification_InputCreatedNotification())->subscribeUser(
                    $userId,
                    [Service_Notification_InputCreatedNotification::PARAM_QUESTION_ID => $qid],
                    Service_Notification_InputCreatedNotification::POSTSUBSCRIBE_ACTION_CONFIRM_IMMEDIATE
                );
                Zend_Registry::get('dbAdapter')->commit();
                $this->flashMessenger->addMessage('Thank you for subscribing.', 'success');
                $this->_redirect('/input/show/kid/' . $kid . '/qid/' . $qid);
            } catch (Exception $e) {
                Zend_Registry::get('dbAdapter')->rollback();
                throw $e;
            }
        } else {
            if (isset($post['email'])) {
                if ($sbsForm->isValid($post)) {
                    Zend_Registry::get('dbAdapter')->beginTransaction();
                    try {
                        list($userId) = (new Model_Users())->register($sbsForm->getValues());
                        (new Service_Notification_InputCreatedNotification())->subscribeUser(
                            $userId,
                            [Service_Notification_InputCreatedNotification::PARAM_QUESTION_ID => $qid],
                            Service_Notification_InputCreatedNotification::POSTSUBSCRIBE_ACTION_CONFIRM_EMAIL_REQUEST
                        );
                        Zend_Registry::get('dbAdapter')->commit();
                        $this->flashMessenger->addMessage(
                            'You are now subscribed. A confirmation email has been sent.',
                            'success'
                        );
                        $this->_redirect('/input/show/kid/' . $kid . '/qid/' . $qid);
                    } catch (Dbjr_Notification_Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        $this->flashMessenger->addMessage('You are already subscribed.', 'success');
                        $this->_redirect('/input/show/kid/' . $kid . '/qid/' . $qid);
                    } catch (Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        throw $e;
                    }
                } else {
                    $this->flashMessenger->addMessage('The subscription form is invalid.', 'error');
                }
            }
        }

        return isset($sbsForm) ? $sbsForm : null;
    }

    /**
     * @param array $post
     * @param int $kid
     * @param int $inputId
     * @param Zend_Auth $auth
     * @param Default_Form_SubscribeNotification $sbsForm
     * @throws \Exception
     * @throws \Zend_Exception
     */
    private function handleSubscribeInputDiscussion(
        array $post,
        $kid,
        $inputId,
        Zend_Auth $auth,
        Default_Form_SubscribeNotification $sbsForm
    ) {
        if ($auth->hasIdentity()) {
            Zend_Registry::get('dbAdapter')->beginTransaction();
            try {
                $userId = $auth->getIdentity()->uid;
                (new Service_Notification_DiscussionContributionCreatedNotification())->subscribeUser(
                    $userId,
                    [Service_Notification_DiscussionContributionCreatedNotification::PARAM_INPUT_ID => $inputId],
                    Service_Notification_DiscussionContributionCreatedNotification::POSTSUBSCRIBE_ACTION_CONFIRM_IMMEDIATE
                );
                Zend_Registry::get('dbAdapter')->commit();
                $this->flashMessenger->addMessage('Thank you for subscribing.', 'success');
                $this->_redirect('/input/discussion/kid/' . $kid . '/inputId/' . $inputId);
            } catch (Exception $e) {
                Zend_Registry::get('dbAdapter')->rollback();
                throw $e;
            }
        } else {
            if (isset($post['email'])) {
                if ($sbsForm->isValid($post)) {
                    Zend_Registry::get('dbAdapter')->beginTransaction();
                    try {
                        list($userId) = (new Model_Users())->register($sbsForm->getValues());
                        (new Service_Notification_DiscussionContributionCreatedNotification())->subscribeUser(
                            $userId,
                            [Service_Notification_DiscussionContributionCreatedNotification::PARAM_INPUT_ID => $inputId],
                            Service_Notification_DiscussionContributionCreatedNotification::POSTSUBSCRIBE_ACTION_CONFIRM_EMAIL_REQUEST
                        );
                        Zend_Registry::get('dbAdapter')->commit();
                        $this->flashMessenger->addMessage(
                            'You are now subscribed. A confirmation email has been sent.',
                            'success'
                        );
                        $this->_redirect('/input/discussion/kid/' . $kid . '/inputId/' . $inputId);
                    } catch (Dbjr_Notification_Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        $this->flashMessenger->addMessage('You are already subscribed.', 'success');
                        $this->_redirect('/input/discussion/kid/' . $kid . '/inputId/' . $inputId);
                    } catch (Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        throw $e;
                    }
                } else {
                    $this->flashMessenger->addMessage('The subscription form is invalid.', 'error');
                }
            }
        }
    }

    /**
     * Handles request to unsubscribe user from recieving notifications of new inputs belonging to this question
     * @param array $post The data received in post request
     * @param int $kid The consultation identifier
     * @param int $qid The question identifier
     * @param \Zend_Auth $auth The auth adapter
     * @param \Default_Form_UnsubscribeNotification $unSbsForm
     */
    private function handleUnsubscribeQuestion(
        array $post,
        $kid,
        $qid,
        Zend_Auth $auth,
        Default_Form_UnsubscribeNotification $unSbsForm
    ) {
        if ($unSbsForm->isValid($post)) {
            $userId = $auth->getIdentity()->uid;
            (new Service_Notification_InputCreatedNotification())->unsubscribeUser(
                $userId,
                [Service_Notification_InputCreatedNotification::PARAM_QUESTION_ID => $qid]
            );
            $this->flashMessenger->addMessage('You have been unsubscribed.', 'success');
            $this->_redirect('/input/show/kid/' . $kid . '/qid/' . $qid);
        }
    }

    /**
     * Handles request to unsubscribe user from recieving notifications of new input discussion contributions
     * @param array $post The data received in post request
     * @param int $kid The consultation identifier
     * @param int $inputId The input identifier
     * @param \Zend_Auth $auth The auth adapter
     * @param \Default_Form_UnsubscribeNotification $unSbsForm
     * @throws \Zend_Form_Exception
     */
    private function handleUnsubscribeInputDiscussion(
        array $post,
        $kid,
        $inputId,
        Zend_Auth $auth,
        Default_Form_UnsubscribeNotification $unSbsForm
    ) {
        if ($unSbsForm->isValid($post)) {
            $userId = $auth->getIdentity()->uid;
            (new Service_Notification_DiscussionContributionCreatedNotification())->unsubscribeUser(
                $userId,
                [Service_Notification_DiscussionContributionCreatedNotification::PARAM_INPUT_ID => $inputId]
            );
            $this->flashMessenger->addMessage('You have been unsubscribed.', 'success');
            $this->_redirect('/input/discussion/kid/' . $kid . '/inputId/' . $inputId);
        }
    }

    /**
     * Saves input in session, called in showAction() if form submitted.
     * If form is valid, it redirects based on the submit button pressed
     * - next question
     * - input confirmation page
     * - same page with field for extra input
     * @param array $post The data received in post request
     * @param int $kid The consultation identifier
     * @param int $qid The qiestion identifier
     */
    private function handleInputSubmit(array $post, $kid, $qid)
    {
        $redirectURL = '/input/show/kid/' . $kid . '/qid/' . $qid;

        $form = $this
            ->getInputForm()
            ->generateInputFields($post['inputs'], false);

        if ($form->isValid($this->_request->getPost())) {
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
                $tmpCollection = [];
            }

            foreach ($post['inputs'] as $input) {
                if (!empty($input['thes']) || !empty($input['video_id'])) {
                    $tmpCollection[] = [
                        'kid' => $kid,
                        'qi' => $qid,
                        'thes' => $input['thes'],
                        'expl' => $input['expl'],
                        'video_service' => !empty($input['video_id']) && isset($input['video_service']) ?
                            $input['video_service'] : null,
                        'video_id' => !empty($input['video_id']) ? $input['video_id'] : null,
                    ];
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
            $msg = Zend_Registry::get('Zend_Translate')->translate(
                'Please make sure the data you entered are correct and that all linked videos are public. Then please try resubmitting the form.'
            );
            $this->flashMessenger->addMessage(
                sprintf(
                    $msg,
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
            // This is needed when creating user with webservice registration
            $sessInputs->kid = $kid;

            $inputModel = new Model_Inputs();
            $confirmKey = $inputModel->getConfirmationKey();

            $inputModel->getAdapter()->beginTransaction();
            try {
                foreach ($sessInputs->inputs as $input) {
                    $input['uid'] = $auth->hasIdentity() ? $auth->getIdentity()->uid : null;
                    $input['confirmation_key'] = $confirmKey;
                    $input['user_conf'] = $auth->hasIdentity() ? 'c' : 'u';
                    $inputModel->add($input);
                }
                $inputModel->getAdapter()->commit();
            } catch (Exception $e) {
                $inputModel->getAdapter()->rollback();
                throw $e;
            }

            if ($auth->hasIdentity()) {
                $qiSent = [];
                foreach ($sessInputs->inputs as $input) {
                    if (!in_array($input['qi'], $qiSent)) {
                        $qiSent[] = $input['qi'];
                        (new Service_Notification_InputCreatedNotification())->notify(
                            [Service_Notification_InputCreatedNotification::PARAM_QUESTION_ID => $input['qi']]
                        );
                    }
                }
            }
            unset($sessInputs->inputs);

            $sessInputs->confirmKey = $confirmKey;
            $registerForm = new Default_Form_Register();
            $registerForm->getElement('kid')->setValue($kid);
            if ($auth->hasIdentity()) {
                $user = (new Model_Users())->fetchRow(
                    (new Model_Users())
                        ->select()
                        ->where('email=?', $auth->getIdentity()->email)
                )->toArray();
                $user['is_contrib_under_cc'] = false;
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
            $this->flashMessenger->addMessage(
                'There is no input to be confirmed.',
                'info'
            );
            $this->redirect('/');
        }

        // Logging in on this page would cause redirect and thus there would be no way to tie them to the user
        // as the session is already emptied
        Zend_Layout::getMvcInstance()->assign(
            'disableLoginMsg',
            Zend_Registry::get('Zend_Translate')->translate('Please finish contributing before logging in.')
        );
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
            $inputs = $inputModel->fetchAll(
                $inputModel
                    ->select()
                    ->from($inputModel->info(Model_Inputs::NAME), ['qi'])
                    ->where('confirmation_key=?', $ckey)
                    ->group('qi')
            );
            $confirmedCount = $inputModel->confirmByCkey($ckey);
            $inputModel->getAdapter()->commit();

            if ($confirmedCount) {
                $this->flashMessenger->addMessage('Thank you! Your inputs have been confirmed!', 'success');

                // we know there are no inputs of the same question because of the groupBy statement in the query
                foreach ($inputs as $input) {
                    (new Service_Notification_InputCreatedNotification())->notify(
                        [Service_Notification_InputCreatedNotification::PARAM_QUESTION_ID => $input['qi']]
                    );
                }
            } else {
                $this->flashMessenger->addMessage('This confirmation link is invalid!', 'error');
            }
            $this->redirect('/');

        } catch (Dbjr_UrlkeyAction_Exception $e) {
            $inputModel->getAdapter()->rollback();
            $this->flashMessenger->addMessage(
                'It is not allowed to confirm inputs once the input phase is over.',
                'error'
            );
            $this->redirect('/');
        } catch (Exception $e) {
            $inputModel->getAdapter()->rollback();
            throw $e;
        }
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

            if ($rejectedCount) {
                $this->flashMessenger->addMessage('The contributions have already been marked as refused!', 'success');
            } else {
                $this->flashMessenger->addMessage('This confirmation link is invalid!', 'error');
            }
            $this->redirect('/');
        } catch (Dbjr_UrlkeyAction_Exception $e) {
            $inputModel->getAdapter()->rollback();
            $this->flashMessenger->addMessage(
                'It is not allowed to reject inputs once the input phase is over.',
                'error'
            );
            $this->redirect('/');
        } catch (Exception $e) {
            $inputModel->getAdapter()->rollback();
            throw $e;
        }
    }

    /**
     * Called by ajax request, switches context to json
     */
    public function supportAction()
    {
        $data = $this->getRequest()->getPost();
        if (empty($data['tid'])) {
            $this->redirect('/');
        }
        $supports = new Zend_Session_Namespace('supports');
        if (empty($supports->clicks)) {
            $supports->clicks = [];
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

        $contributionModel = new Model_Inputs();
        $contribution = $contributionModel->getById($tid);
        if (empty($contribution)) {
            $this->flashMessenger->addMessage('Contribution does not exist', 'error');
            $this->redirect('/');
        }

        if (Zend_Date::now()->isEarlier(new Zend_Date($this->consultation->inp_to, Zend_Date::ISO_8601))) {
            // allow editing only BEFORE inputs period is over
            $form = new Default_Form_Input_Edit();
            $question = (new Model_Questions())->find($contribution['qi'])->current();
            $form->setVideoEnabled($question['video_enabled'] && (new Model_Projects())->getVideoServiceStatus());

            if ($this->_request->isPost()) {
                // form submitted
                $data = $this->_request->getPost();
                if ($form->isValid($data)) {
                    $key = $contributionModel->updateById($tid, $data);
                    if ($key > 0) {
                        $this->flashMessenger->addMessage('Contribution updated.', 'success');
                    } else {
                        $this->flashMessenger->addMessage(
                            'Something went wrong: contribution could not be updated.',
                            'error'
                        );
                    }
                    $this->redirect(
                        $this->view->url([
                            'controller' => 'user',
                            'action' => 'activity',
                        ]),
                        ['prependBase' => false]
                    );
                } else {
                    $this->flashMessenger->addMessage('Please check your data!', 'error');
                    $form->populate($data);
                }
            } else {
                $data = ['thes' => $contribution['thes'], 'expl' => $contribution['expl']];
                if ($contribution['video_service'] !== null) {
                    $project = (new Model_Projects)->find((new Zend_Registry())->get('systemconfig')->project)
                        ->current();
                    if ($project['video_' . $contribution['video_service'] . '_enabled']) {
                        $data['video_service'] = $contribution['video_service'];
                        $data['video_id'] = $contribution['video_id'];
                    } else {
                        $this->flashMessenger->addMessage(
                            'Video service used for embedding video in this contribution was disabled. Video will be deleted after save contribution.',
                            'error'
                        );
                    }
                }
                $form->populate($data);
            }
            $this->view->form = $form;
        } else {
            // inputs period is already over
            $this->view->message = $this->view->translate(
                'Sorry, the contribution phase for this consultation round is already over.'
                . 'You may only change your contributions within that period.'
            );
        }
    }

    public function tagsAction()
    {
        $kid = $this->_request->getParam('kid', 0);
        $inputModel = new Model_Inputs();
        $tagModel = new Model_Tags();

        $this->view->inputCount = $inputModel->getCountByConsultation($this->consultation->kid);
        $this->view->tags = $tagModel->getAllByConsultation($kid);
    }

    /**
     * Displays the discussion page and processes discussion contribution additions
     */
    public function discussionAction()
    {
        $inputId = $this->getRequest()->getParam('inputId', null);
        if (!$inputId) {
            $this->_redirect('/');
        }

        $auth = Zend_Auth::getInstance();

        $inputDiscussModel = new Model_InputDiscussion();
        $inputsModel = new Model_Inputs();

        $form = new Default_Form_Input_Discussion(null, $this->consultation['discussion_video_enabled']);
        if($auth->hasIdentity()){
            $form->populate(['email' => $auth->getIdentity()->email]);
            $form->getElement('email')->setAttrib('disabled', 'disabled');
        }

        $sbsForm = (new Service_Notification_SubscriptionFormFactory())->getForm(
            new Service_Notification_DiscussionContributionCreatedNotification(),
            [Service_Notification_DiscussionContributionCreatedNotification::PARAM_INPUT_ID => $inputId]
        );

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->view->userIdentity = $auth->getIdentity();
        }

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if($auth->hasIdentity()){
                $post['email'] = $auth->getIdentity()->email;
            }
            if (isset($post['subscribe'])) {
                $this->handleSubscribeInputDiscussion($post, $this->consultation->kid, $inputId, $auth, $sbsForm);
            } elseif (isset($post['unsubscribe']) && $auth->hasIdentity()) {
                $this->handleUnsubscribeInputDiscussion($post, $this->consultation->kid, $inputId, $auth, $sbsForm);
            } elseif (Zend_Date::now()->isLater(new Zend_Date($this->consultation->discussion_from, Zend_Date::ISO_8601))
                && Zend_Date::now()->isEarlier(new Zend_Date($this->consultation->discussion_to, Zend_Date::ISO_8601))
            ) {
                if ($form->isValid($post)) {
                    Zend_Registry::get('dbAdapter')->beginTransaction();
                    $formData = $form->getValues();
                    $isNew = false;
                    try {
                        if ($auth->hasIdentity()) {
                            $msg = 'Your discussion post was saved.';
                            $userId = $auth->getIdentity()->uid;
                        } else {
                            $msg = 'Your discussion post was saved. A confirmation email has been sent.';
                            list($userId, $isNew) = (new Model_Users())->register(['email' => $formData['email']]);
                        }

                        $contribId = $inputDiscussModel->insert([
                            'body' => $formData['body'] ? $formData['body'] : null,
                            'video_service' => isset($formData['video_service']) && $formData['video_service'] ?
                                $formData['video_service'] : null,
                            'video_id' => isset($formData['video_id']) && $formData['video_id'] ?
                                $formData['video_id'] : null,
                            'user_id' => $userId,
                            'is_user_confirmed' => $auth->hasIdentity() ? true : false,
                            'is_visible' => true,
                            'input_id' => $inputId,
                        ]);

                        if (!$auth->hasIdentity()) {
                            $action = (new Service_UrlkeyAction_ConfirmInputDiscussionContribution())->create(
                                [Service_UrlkeyAction_ConfirmInputDiscussionContribution::PARAM_DISCUSSION_CONTRIBUTION_ID => $contribId]
                            );

                            $user = (new Model_Users())->find($userId)->current();
                            $template = Model_Mail_Template::SYSTEM_TEMPLATE_INPUT_DISCUSSION_CONTRIB_CONFIRMATION;
                            if ($isNew && $user->block === 'u') {
                                $template = Model_Mail_Template::SYSTEM_TEMPLATE_INPUT_DISCUSSION_CONTRIB_CONFIRMATION_NEW_USER;
                            }

                            $mailer = new Dbjr_Mail();
                            $mailer
                                ->setTemplate($template)
                                ->setPlaceholders([
                                    'to_name' => $user->name ? $user->name : $user->email,
                                    'to_email' => $user->email,
                                    'contribution_text' => $formData['body'],
                                    'video_url' => !empty($formData['video_service']) && !empty($formData['video_id']) ?
                                        sprintf(
                                            Zend_Registry::get('systemconfig')->video->url->{$formData['video_service']}
                                                ->format->link,
                                            $formData['video_id']
                                        ) : '',
                                    'confirmation_url' => Zend_Registry::get('baseUrl')
                                        . '/urlkey-action/execute/urlkey/' . $action->getUrlkey(),
                                ])
                                ->addTo($user->email);
                            (new Service_Email)
                                ->queueForSend($mailer)
                                ->sendQueued();
                        } else {
                            (new Service_Notification_DiscussionContributionCreatedNotification())->notify(
                                [Service_Notification_DiscussionContributionCreatedNotification::PARAM_INPUT_ID => $inputId]
                            );
                        }

                        Zend_Registry::get('dbAdapter')->commit();
                        $this->flashMessenger->addMessage($msg, 'success');
                        $this->_redirect($this->view->url(), ['prependBase' => false]);
                    } catch (Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        throw $e;
                    }
                } else {
                    $this->flashMessenger->addMessage('Please check your data!', 'error');
                }
            }
        }

        $this->view->form = $form;
        $this->view->subscriptionForm = $sbsForm;
        $this->view->videoServicesStatus = (new Model_Projects())->find(
            (new Zend_Registry())->get('systemconfig')->project
        )->current();
        $this->view->discussionContribs = $inputDiscussModel->fetchAll(
            $inputDiscussModel
                ->select()
                ->setIntegrityCheck(false)
                ->from(
                    ['i' => $inputDiscussModel->info(Model_InputDiscussion::NAME)],
                    ['user_id', 'time_created', 'body', 'is_visible', 'video_service', 'video_id', 'id']
                )
                ->where('input_id=?', $inputId)
                ->where('is_user_confirmed=?', 1)
                ->join(
                    (new Model_Users())->info(Model_Users::NAME),
                    (new Model_Users())->info(Model_Users::NAME) . '.uid = i.user_id',
                    ['uid', 'name', 'nick']
                )
                ->order('time_created ASC')
        );
        $input = $inputsModel->fetchRow(
            $inputsModel
                ->select()
                ->where('tid=?', $inputId)
        );

        if (!$input) {
            $this->_redirect('/');
        }

        $this->view->input = $input;
        $this->view->consultation = $this->consultation;
    }

    /**
     * @return \Default_Form_Input_Create
     */
    private function getInputForm()
    {
        if (null === $this->inputForm) {
            $this->inputForm = new Default_Form_Input_Create();
        }

        return $this->inputForm;
    }
}
