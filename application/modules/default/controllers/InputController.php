<?php

use VideoIdExtractor\Exception\VideoIdExtractException;
use VideoIdExtractor\Extractor\FacebookVideoIdExtractor;
use VideoIdExtractor\Extractor\VimeoVideoIdExtractor;
use VideoIdExtractor\Extractor\YoutubeVideoIdExtractor;

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
            $questions[$key]['inputs'] = $inputModel->getByQuestion($question['qi'], 'tid DESC', 4, null, true);
        }

        $this->view->questions = $questions;
        $this->view->nowDate = Zend_Date::now();
        $this->view->inputCount = $inputModel->getCountByConsultation($this->consultation->kid);
        $this->view->tags = (new Model_Tags())->getAllByConsultation($kid, '');
    }
    public function showAction()
    {
        $inputModel = new Model_Inputs();
        $questionModel = new Model_Questions();
        $listType = $this->_getParam('type', null);
        $kid = $this->_getParam('kid', 0);
        $qid = $this->_getParam('qid', $questionModel->getByConsultation($kid)->current()->qi);
        $tag = $this->_getParam('tag');

        $auth = Zend_Auth::getInstance();
        $sbsForm = (new Service_Notification_SubscriptionFormFactory())->getForm(
            new Service_Notification_InputCreatedNotification(),
            [Service_Notification_InputCreatedNotification::PARAM_QUESTION_ID => $qid]
        );

        $question = $questionModel->find($qid)->current();
        $form = $this->getInputForm();
        $form->setQuestion($question->toArray());
        if (!$question['location_enabled'] && $listType === 'map') {
            $this->redirect($this->view->url([
                'controller' => 'input',
                'action' => 'show',
                'kid' => $kid,
                'qid' => $qid,
                'type' => null,
                'tag' => $tag,
            ]));
        }
        $form->setLocationEnabled($question['location_enabled']);
        $form->setVideoEnabled($question['video_enabled']);

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if (isset($post['subscribe'])) {
                $this->handleSubscribeQuestion($post, $kid, $qid, $auth, $sbsForm, $listType);
            } elseif (isset($post['unsubscribe']) && $auth->hasIdentity()) {
                $this->handleUnsubscribeQuestion($post, $kid, $qid, $auth, $sbsForm, $listType);
            } elseif (isset($post['finished']) || isset($post['next_question'])) {
                $post['inputs'] = $this->extractVideoIds($post['inputs']);
                $this->handleInputSubmit($post, $kid, $qid, $listType);
            } else {
                $form = $this->getInputForm();
                $inputModel = new Model_Inputs();
                $contributions = [];
                $sessInputs = (new Zend_Session_Namespace('inputs'));
                if (isset($sessInputs->confirmKey)) {
                    $contributions = $inputModel->getByConfirmKeyAndQuestion($sessInputs->confirmKey, $question['qi']);
                }
                $form->generateInputFields($contributions);
                $form->setAction($listType === null
                    ? sprintf(
                        '%s/input/show/kid/%d/qid/%d',
                        $this->view->baseUrl(),
                        $kid,
                        $qid
                    )
                    : sprintf(
                        '%s/input/show/kid/%d/qid/%d/type/%s',
                        $this->view->baseUrl(),
                        $kid,
                        $qid,
                        $listType
                    ));
            }
        } elseif (Zend_Date::now()->isLater(new Zend_Date($this->consultation->inp_fr, Zend_Date::ISO_8601))
            && Zend_Date::now()->isEarlier(new Zend_Date($this->consultation->inp_to, Zend_Date::ISO_8601))
        ) {
            $form = $this->getInputForm();
            $inputModel = new Model_Inputs();
            $contributions = [];
            $sessInputs = (new Zend_Session_Namespace('inputs'));
            if (isset($sessInputs->confirmKey)) {
                $contributions = $inputModel->getByConfirmKeyAndQuestion($sessInputs->confirmKey, $question['qi']);
            }
            $form->generateInputFields($contributions);
            $form->setAction($listType === null
                ? sprintf(
                    '%s/input/show/kid/%d/qid/%d',
                    $this->view->baseUrl(),
                    $kid,
                    $qid
                )
                : sprintf(
                    '%s/input/show/kid/%d/qid/%d/type/%s',
                    $this->view->baseUrl(),
                    $kid,
                    $qid,
                    $listType
                ));
        }

        $project = (new Model_Projects())->find((new Zend_Registry())->get('systemconfig')->project)->current();

        if ($listType === 'map') {
            $this->view->inputs = $inputModel->getByQuestion($qid, 'i.tid ASC', null, $tag, true);
        } else {
            $paginator = Zend_Paginator::factory($inputModel->getSelectByQuestion($qid, 'i.tid ASC', null, $tag, true));
            $maxPage = ceil($paginator->getTotalItemCount() / $paginator->getItemCountPerPage());
            $paginator->setCurrentPageNumber($this->_getParam('page', $maxPage));

            $this->view->paginator = $paginator;
        }
        
        $this->view->videoServicesStatus = $project;
        $this->view->videoEnabled = $question['video_enabled'];
        $this->view->subscriptionForm = $sbsForm;
        $this->view->tag = $tag ? (new Model_Tags())->getById($tag) : null;
        $this->view->inputForm = isset($form) ? $form : null;
        $this->view->numberInputs = $inputModel->getCountByQuestion($qid, $tag);
        $this->view->question = $questionModel->getById($qid);
        $this->view->listType = $listType;
    }

    /**
     * Handles request to subscribe user to recieve notifications of new inputs belonging to this question
     * @param array $post The data received in post request
     * @param int $kid The consultation identifier
     * @param int $qid The qiestion identifier
     * @param \Zend_Auth $auth The auth adapter
     * @param \Default_Form_SubscribeNotification $sbsForm
     * @param string $listType
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
        Default_Form_SubscribeNotification $sbsForm,
        $listType
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
                $this->redirect($this->view->url([
                    'controller' => 'input',
                    'action' => 'show',
                    'kid' => $kid,
                    'qid' => $qid,
                    'type' => $listType,
                ]));
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
                        $this->redirect($this->view->url([
                            'controller' => 'input',
                            'action' => 'show',
                            'kid' => $kid,
                            'qid' => $qid,
                            'type' => $listType,
                        ]));
                    } catch (Dbjr_Notification_Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        $this->flashMessenger->addMessage('You are already subscribed.', 'success');
                        $this->redirect($this->view->url([
                            'controller' => 'input',
                            'action' => 'show',
                            'kid' => $kid,
                            'qid' => $qid,
                            'type' => $listType,
                        ]));
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
     * @param string $listType
     * @param \Zend_Auth $auth The auth adapter
     * @param \Default_Form_UnsubscribeNotification $unSbsForm
     */
    private function handleUnsubscribeQuestion(
        array $post,
        $kid,
        $qid,
        Zend_Auth $auth,
        Default_Form_UnsubscribeNotification $unSbsForm,
        $listType
    ) {
        if ($unSbsForm->isValid($post)) {
            $userId = $auth->getIdentity()->uid;
            (new Service_Notification_InputCreatedNotification())->unsubscribeUser(
                $userId,
                [Service_Notification_InputCreatedNotification::PARAM_QUESTION_ID => $qid]
            );
            $this->flashMessenger->addMessage('You have been unsubscribed.', 'success');
            $this->redirect($this->view->url([
                'controller' => 'input',
                'action' => 'show',
                'kid' => $kid,
                'qid' => $qid,
                'type' => $listType,
            ]));
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
        $form = $this
            ->getInputForm()
            ->generateInputFields($post['inputs'], false);

        $question = (new Model_Questions())->find($qid)->current();
        if (!$question) {
            $this->flashMessenger->addMessage('Question was not found.', 'error');
            $this->redirect($this->view->url(['action' => 'show']), ['prependBase' => false]);
        }

        if ($form->isValid($post)) {
            $sessInputs = (new Zend_Session_Namespace('inputs'));

            $inputModel = new Model_Inputs();
            if (!isset($sessInputs->confirmKey)) {
                $confirmKey = $inputModel->getConfirmationKey();
                $sessInputs->confirmKey = $confirmKey;
            } else {
                $confirmKey = $sessInputs->confirmKey;
            }

            $notSavedContributions = [];
            $inputModel->getAdapter()->beginTransaction();
            try {
                $errorShown = false;
                $errorGeoFenceShown = false;
                $successShown = false;
                foreach ($post['inputs'] as $input) {
                    if (!empty($input['video_id']) && empty($input['thes'])) {
                        $notSavedContributions[] = $input;
                        if (!$errorShown) {
                            $this->flashMessenger->addMessage(
                                'Contribution text cannot be empty.',
                                'error'
                            );
                            $errorShown = true;
                        }
                        continue;
                    }

                    if (!empty($input['thes'])) {
                        $input['kid'] = $kid;
                        $input['qi'] = $qid;
                        $input['video_service'] = (!empty($input['video_id']) && isset($input['video_service']))
                            ? $input['video_service']
                            : null;
                        $input['video_id'] = !empty($input['video_id']) ? $input['video_id'] : null;
                        $input['latitude'] = !empty($input['latitude']) ? (float) $input['latitude'] : null;
                        $input['longitude'] = !empty($input['longitude']) ? (float) $input['longitude'] : null;
                        $input['confirmation_key'] = $confirmKey;
                        if ($question['geo_fence_enabled'] && $input['latitude'] !== null
                            && $input['longitude'] !== null
                        ) {
                            $geoFence = json_decode($question['geo_fence_polygon']);
                            if (count($geoFence)) {
                                if (!(new Service_PointInPolygon())->isPointInPolygon(
                                    $input['latitude'],
                                    $input['longitude'],
                                    $geoFence
                                )) {
                                    $notSavedContributions[] = $input;
                                    if (!$errorGeoFenceShown) {
                                        $this->flashMessenger->addMessage(
                                            'Location cannot be out of marked geo fence.',
                                            'error'
                                        );
                                        $errorGeoFenceShown = true;
                                    }
                                    continue;
                                }
                            }
                        }
                        if (!empty($input['tid'])) {
                            $inputModel->updateById($input['tid'], $input);
                        } else {
                            $inputModel->add($input);
                        }
                        if (!$successShown) {
                            $this->flashMessenger->addMessage(
                                'Contributions were saved.',
                                'success'
                            );
                            $successShown = true;
                        }
                    }
                }
                $inputModel->getAdapter()->commit();
            } catch (Exception $e) {
                $inputModel->getAdapter()->rollback();
                throw $e;
            }

            if ($successShown && !$errorShown && isset($post['next_question'])) {
                $nextQuestion = (new Model_Questions())->getNext($qid);
                $this->redirect('/input/show/kid/' . $kid . ($nextQuestion ? '/qid/' . $nextQuestion['qi'] : ''));
            } elseif ($successShown && !$errorShown && isset($post['finished'])) {
                if ($this->consultation['anonymous_contribution']) {
                    $this->redirect('/input/finished/kid/' . $kid);
                } else {
                    $this->redirect('/user/register/kid/' . $kid);
                }
            }
            $form->populate($post);
            $this->flashMessenger->addMessage(
                Zend_Registry::get('Zend_Translate')->translate(
                    'There are no contributions to save. Please add at least one contribution before you click on finish.'
                ),
                'error'
            );

            return;
        }

        $form->populate($post);

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

    public function finishedAction()
    {
        $sessInputs = (new Zend_Session_Namespace('inputs'));
        unset($sessInputs->confirmKey);
        $this->view->info = $this->consultation['anonymous_contribution_finish_info'];
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
                    ->setIntegrityCheck(false)
                    ->from(['i' => $inputModel->info(Model_Inputs::NAME)], ['qi'])
                    ->join(['q' => (new Model_Questions())->info(Model_Questions::NAME)], 'i.qi = q.qi', ['kid'])
                    ->where('confirmation_key=?', $ckey)
                    ->group('qi')
            );
            $userInfo = (new Model_User_Info())->fetchRow(['confirmation_key = ?' => $ckey]);
            if (!$userInfo) {
                $this->flashMessenger->addMessage('This confirmation link is invalid!', 'error');
                $this->redirect('/');
            }

            $confirmedCount = $inputModel->confirmByCkey($ckey);

            if ($inputs->count() > 0) {
                (new Model_Votes_Rights())
                    ->setInitialRightsForConfirmedUser($userInfo['uid'], $inputs->current()['kid']);
            }
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
            $form = new Default_Form_Input_Edit(new Service_RequestInfo());
            $question = (new Model_Questions())->find($contribution['qi'])->current();
            $form->setQuestion($question->toArray());
            $form->setVideoEnabled($question['video_enabled'] && (new Model_Projects())->getVideoServiceStatus());
            $form->setLocationEnabled($question['location_enabled']);

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
                $data = [
                    'thes' => $contribution['thes'],
                    'expl' => $contribution['expl'],
                    'latitude' => $contribution['latitude'],
                    'longitude' => $contribution['longitude'],
                    'location_enabled' => $contribution['latitude'] !== null,
                ];
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
                $post = $this->extractVideoId($post);
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
                            'is_user_confirmed' => (int) ($auth->hasIdentity() ? true : false),
                            'is_visible' => true,
                            'input_id' => $inputId,
                        ]);

                        if (!$auth->hasIdentity()) {
                            $action = (new Service_UrlkeyAction_ConfirmInputDiscussionContribution())->create(
                                [Service_UrlkeyAction_ConfirmInputDiscussionContribution::PARAM_DISCUSSION_CONTRIBUTION_ID => $contribId]
                            );

                            $user = (new Model_Users())->find($userId)->current();
                            $template = Model_Mail_Template::SYSTEM_TEMPLATE_INPUT_DISCUSSION_CONTRIB_CONFIRMATION;
                            if ($isNew && $user->is_confirmed === null) {
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
                ->where('is_user_confirmed = ?', true)
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
        $this->view->question = (new Model_Questions())->find($input['qi'])->current();
        $this->view->consultation = $this->consultation;
    }

    /**
     * @return \Default_Form_Input_Create
     */
    private function getInputForm()
    {
        if (null === $this->inputForm) {
            $this->inputForm = new Default_Form_Input_Create(new Service_RequestInfo());
        }

        return $this->inputForm;
    }

    /**
     * @param $inputs
     * @return array
     */
    private function extractVideoIds(array $inputs)
    {
        $preparedInputs = $inputs;
        foreach ($inputs as $key => $input) {
            $preparedInputs[$key] = $this->extractVideoId($input);
        }

        return $preparedInputs;
    }

    /**
     * @param array $input
     * @return array
     */
    private function extractVideoId(array $input)
    {
        try {
            if (isset($input['video_service'])) {
                if ($input['video_service'] === 'youtube') {
                    $input['video_id'] = (new YoutubeVideoIdExtractor())->extract($input['video_id']);
                } elseif ($input['video_service'] === 'vimeo') {
                    $input['video_id'] = (new VimeoVideoIdExtractor())->extract($input['video_id']);
                } elseif ($input['video_service'] === 'facebook') {
                    $input['video_id'] = (new FacebookVideoIdExtractor())->extract($input['video_id']);
                }
            }
        } catch (VideoIdExtractException $e) {

        }

        return $input;
    }
}
