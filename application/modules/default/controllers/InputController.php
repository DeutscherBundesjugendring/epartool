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
                $this->_flashMessenger->addMessage('No consultation provided!', 'error');
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

        if (empty($qid)) {
            $questionRow = $questionModel->getByConsultation($kid)->current();
            $qid = $questionRow->qi;
        }

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

        if (!empty($tag)) {
            $tagModel = new Model_Tags();
            $this->view->tag = $tagModel->getById($tag);
        }

        if (Zend_Date::now()->isLater(new Zend_Date($this->_consultation->inp_fr, Zend_Date::ISO_8601))
            && Zend_Date::now()->isEarlier(new Zend_Date($this->_consultation->inp_to, Zend_Date::ISO_8601))
        ) {
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

        $isSubsribed = false;
        if ($auth->hasIdentity()) {
            $isSubsribed = (new Service_Notification_Input_Created())->isSubscribed(
                $auth->getIdentity()->uid,
                [Service_Notification_Input_Created::PARAM_QUESTION_ID => $qid]
            );
        }

        $this->view->subscriptionForm = isset($sbsForm) ? $sbsForm : $this->_getSubscriptionForm($isSubsribed);
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
            $sbsForm = $this->_getSubscriptionForm(false);
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
                        $this->_flashMessenger->addMessage('You are now subscribed. A confirmation email has been sent.', 'success');
                        $this->_redirect('/input/show/kid/' . $kid . '/qid/' . $qid);
                    } catch (Dbjr_Notification_Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        $this->_flashMessenger->addMessage('You are already subscribed.', 'success');
                        $this->_redirect('/input/show/kid/' . $kid . '/qid/' . $qid);
                    } catch (Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        throw $e;
                    }
                } else {
                    $this->_flashMessenger->addMessage('The subscription form is invalid.', 'error');
                }
            }
        }

        return isset($sbsForm) ? $sbsForm : null;
    }

    /**
     * Handles request to subscribe user to recieve notifications of new inputs belonging to this question
     * @param $post     The data received in post request
     * @param $kid      The consultation identifier
     * @param $inputId  The qiestion identifier
     * @param $auth     The auth adapter
     */
    private function _handleSubscribeInputDiscussion($post, $kid, $inputId, $auth)
    {
        if ($auth->hasIdentity()) {
            Zend_Registry::get('dbAdapter')->beginTransaction();
            try {
                $userId = $auth->getIdentity()->uid;
                (new Service_Notification_Input_DiscussionContributionCreated())->subscribeUser(
                    $userId,
                    [Service_Notification_Input_DiscussionContributionCreated::PARAM_INPUT_ID => $inputId],
                    Service_Notification_Input_DiscussionContributionCreated::POSTSUBSCRIBE_ACTION_CONFIRM_IMMEDIATE
                );
                Zend_Registry::get('dbAdapter')->commit();
                $this->_flashMessenger->addMessage('Thank you for subscribing.', 'success');
                $this->_redirect('/input/discussion/kid/' . $kid . '/inputId/' . $inputId);
            } catch (Exception $e) {
                Zend_Registry::get('dbAdapter')->rollback();
                throw $e;
            }
        } else {
            $sbsForm = $this->_getSubscriptionForm(false);
            if (isset($post['email'])) {
                if ($sbsForm->isValid($post)) {
                    Zend_Registry::get('dbAdapter')->beginTransaction();
                    try {
                        list($userId, $isNew) = (new Model_Users())->register($sbsForm->getValues());
                        (new Service_Notification_Input_DiscussionContributionCreated())->subscribeUser(
                            $userId,
                            [Service_Notification_Input_DiscussionContributionCreated::PARAM_INPUT_ID => $inputId],
                            Service_Notification_Input_DiscussionContributionCreated::POSTSUBSCRIBE_ACTION_CONFIRM_EMAIL_REQUEST
                        );
                        Zend_Registry::get('dbAdapter')->commit();
                        $this->_flashMessenger->addMessage('You are now subscribed. A confirmation email has been sent.', 'success');
                        $this->_redirect('/input/discussion/kid/' . $kid . '/inputId/' . $inputId);
                    } catch (Dbjr_Notification_Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        $this->_flashMessenger->addMessage('You are now subscribed.', 'success');
                        $this->_redirect('/input/discussion/kid/' . $kid . '/inputId/' . $inputId);
                    } catch (Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        throw $e;
                    }
                } else {
                    $this->_flashMessenger->addMessage('The subscription form is invalid.', 'error');
                }
            }
        }

        return isset($sbsForm) ? $sbsForm : null;
    }

    /**
     * Handles request to unsubscribe user from recieving notifications of new inputs belonging to this question
     * @param $post  The data received in post request
     * @param $kid   The consultation identifier
     * @param $qid   The question identifier
     * @param $auth  The auth adapter
     */
    private function _handleUnsubscribeQuestion($post, $kid, $qid, $auth)
    {
        $unsbsForm = new Default_Form_UnsubscribeNotification();
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
     * Handles request to unsubscribe user from recieving notifications of new input discussion contributions
     * @param $post     The data received in post request
     * @param $kid      The consultation identifier
     * @param $inputId  The input identifier
     * @param $auth     The auth adapter
     */
    private function _handleUnsubscribeInputDiscussion($post, $kid, $inputId, $auth)
    {
        $unsbsForm = new Default_Form_UnsubscribeNotification();
        if ($unsbsForm->isValid($post)) {
            $userId = $auth->getIdentity()->uid;
            (new Service_Notification_Input_DiscussionContributionCreated())->unsubscribeUser(
                $userId,
                [Service_Notification_Input_DiscussionContributionCreated::PARAM_INPUT_ID => $inputId]
            );
            $this->_flashMessenger->addMessage('You have been unsubscribed.', 'success');
            $this->_redirect('/input/discussion/kid/' . $kid . '/inputId/' . $inputId);
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
            $msg = Zend_Registry::get('Zend_Translate')->translate(
                'Please check your data. It is also possible that the maximum editing period of %s minutes has exceeded.'
            );
            $this->_flashMessenger->addMessage(
                sprintf($msg, number_format(Zend_Registry::get('systemconfig')->form->input->csfr_protect->ttl / 60, 0)),
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

                if ($auth->hasIdentity()) {
                    $qiSent = [];
                    foreach ($sessInputs->inputs as $input) {
                        if (!in_array($input['qi'], $qiSent)) {
                            $qiSent[] = $input['qi'];
                            (new Service_Notification_Input_Created())->notify(
                                [Service_Notification_Input_Created::PARAM_QUESTION_ID => $input['qi']]
                            );
                        }
                    }
                }
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
            $this->_flashMessenger->addMessage(
                'There is no input to be confirmed.',
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
            $inputs = $inputModel->fetchAll(
                $inputModel
                    ->select($inputModel->info(Model_Inputs::NAME), ['qi'])
                    ->where('confirmation_key=?', $ckey)
                    ->group('qi')
            );
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
            $this->_flashMessenger->addMessage('Thank you! Your inputs have been confirmed!', 'success');

            // we know there are no inputs of the same question because of the groupBy statement in the query
            foreach ($inputs as $input) {
                (new Service_Notification_Input_Created())->notify(
                    [Service_Notification_Input_Created::PARAM_QUESTION_ID => $input['qi']]
                );
            }
        } else {
            $this->_flashMessenger->addMessage('This confirmation link is invalid!', 'error');
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
            $this->_flashMessenger->addMessage('The contributions have already been marked as refused!', 'success');
        } else {
            $this->_flashMessenger->addMessage('This confirmation link is invalid!', 'error');
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
            $this->_flashMessenger->addMessage('Page not found', 'error');
            $this->redirect('/');
        }
        if (Zend_Date::now()->isEarlier(new Zend_Date($this->_consultation->inp_to, Zend_Date::ISO_8601))) {
            // allow editing only BEFORE inputs period is over
            $form = new Default_Form_Input_Edit();
            if ($this->_request->isPost()) {
                // form submitted
                $data = $this->_request->getPost();
                if ($form->isValid($data)) {
                    $key = $inputsModel->updateById($tid, $data);
                    if ($key > 0) {
                        $this->_flashMessenger->addMessage('Contribution updated.', 'success');
                    } else {
                        $this->_flashMessenger->addMessage(
                            'Something went wrong: contribution could not be updated.',
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
                    $this->_flashMessenger->addMessage('Please check your data!', 'error');
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
            $this->view->message = $this->view->translate('Sorry, the contribution phase for this consultation round is already over. You may only change your contributions within that period.');
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

    /**
     * Displays the discussion page and processes discussion contribution additions
     */
    public function discussionAction()
    {
        $inputId = $this->getRequest()->getParam('inputId', null);
        if (!$inputId) {
            $this->_redirect('/');
        }
        $inputDiscussModel = new Model_InputDiscussion();
        $inputsModel = new Model_Inputs();
        $form = new Default_Form_Input_Discussion();
        $isSubsribed = false;
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $form->removeElement('email');
            $isSubsribed = (new Service_Notification_Input_DiscussionContributionCreated())->isSubscribed(
                $auth->getIdentity()->uid,
                [Service_Notification_Input_DiscussionContributionCreated::PARAM_INPUT_ID => $inputId]
            );
        }

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if (isset($post['subscribe'])) {
                $sbsForm = $this->_handleSubscribeInputDiscussion($post, $this->_consultation['kid'], $inputId, $auth);
            } elseif (isset($post['unsubscribe']) && $auth->hasIdentity()) {
                $this->_handleUnsubscribeInputDiscussion($post, $this->_consultation['kid'], $inputId, $auth);
            } elseif (Zend_Date::now()->isLater(new Zend_Date($this->_consultation->discussion_from, Zend_Date::ISO_8601))
                && Zend_Date::now()->isEarlier(new Zend_Date($this->_consultation->discussion_to, Zend_Date::ISO_8601))
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

                        $contribId = $inputDiscussModel->insert(
                            [
                                'body' => $formData['body'],
                                'user_id' => $userId,
                                'is_user_confirmed' => $auth->hasIdentity() ? true : false,
                                'is_visible' => true,
                                'input_id' => $inputId,
                            ]
                        );

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
                                ->setPlaceholders(
                                    array(
                                        'to_name' => $user->name ? $user->name : $user->email,
                                        'to_email' => $user->email,
                                        'contribution_text' => $formData['body'],
                                        'confirmation_url' =>  Zend_Registry::get('baseUrl') . '/urlkey-action/execute/urlkey/' . $action->getUrlkey(),
                                    )
                                )
                                ->addTo($user->email);
                            (new Service_Email)
                                ->queueForSend($mailer)
                                ->sendQueued();
                        } else {
                            (new Service_Notification_Input_DiscussionContributionCreated())->notify(
                                [Service_Notification_Input_DiscussionContributionCreated::PARAM_INPUT_ID => $inputId]
                            );
                        }

                        Zend_Registry::get('dbAdapter')->commit();
                        $this->_flashMessenger->addMessage($msg, 'success');
                        $this->_redirect($this->view->url(), ['prependBase' => false]);
                    } catch (Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        throw $e;
                    }
                } else {
                    $this->_flashMessenger->addMessage('Please check your data!', 'error');
                }
            }
        }

        $this->view->form = $form;
        $this->view->subscriptionForm = isset($sbsForm) ? $sbsForm : $this->_getSubscriptionForm($isSubsribed);
        $this->view->discussionContribs = $inputDiscussModel->fetchAll(
            $inputDiscussModel
                ->select()
                ->from(
                    ['i' => $inputDiscussModel->info(Model_InputDiscussion::NAME)],
                    ['user_id', 'time_created', 'body', 'is_visible']
                )
                ->where('input_id=?', $inputId)
                ->where('is_user_confirmed=?', 1)
                ->setIntegrityCheck(false)
                ->join(
                    (new Model_Users())->info(Model_Users::NAME),
                    (new Model_Users())->info(Model_Users::NAME) . '.uid = i.user_id',
                    ['uid', 'name']
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
    private function _getSubscriptionForm($isSubscribed)
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity() && $isSubscribed) {
            $form = new Default_Form_UnsubscribeNotification();
        } else {
            $form = new Default_Form_SubscribeNotification();
            if (!$auth->hasIdentity()) {
                $form->requireId();
            }
        }

        return $form;
    }
}
