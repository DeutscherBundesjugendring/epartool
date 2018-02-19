<?php
/**
 * UserController
 *
 */
class UserController extends Zend_Controller_Action
{
    protected $_auth = null;

    protected $_flashMessenger = null;

    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();
        $this->_flashMessenger = $this->_helper->getHelper('flashMessenger');

        $kid = $this->getRequest()->getParam('kid', 0);
        $consultationModel = new Model_Consultations();
        $consultation = $consultationModel->find($kid)->current();
        if ($consultation) {
            $this->_consultation = $consultation;
        }
    }

    /**
     * Abmeldung
     *
     * @return void
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_flashMessenger->addMessage('Logout successful!', 'info');
        $this->redirect('/');
    }

    /**
     * Register
     */
    public function registerAction()
    {
        $kid = $this->_getParam('kid', null);
        if (!$kid) {
            throw new Zend_Controller_Action_Exception('No consultation found.', 404);
        }

        $auth = Zend_Auth::getInstance();
        $sessInputs = new Zend_Session_Namespace('inputs');

        $this->view->infoText = $this->_consultation['contribution_confirmation_info'];
        $this->view->consultation = $this->_consultation;

        $registerForm = new Default_Form_Register($this->_consultation['kid']);
        if ($this->_auth->hasIdentity()) {
            $registerForm->getElement('email')
                ->setRequired(false)
                ->setAttrib('disabled', 'disabled');
        }

        if ($sessInputs->confirmKey) {
            if ($this->_request->isPost()) {
                $rawData = $this->_request->getPost();
                $userModel = new Model_Users();
                if ($registerForm->isValid($rawData)) {
                    $data = $registerForm->getValues();
                    if ($data['group_type'] !== 'group') {
                        unset($data['group_specs']);
                    }
                    unset($data['group_type']);

                    $confirmKey = (new Zend_Session_Namespace('inputs'))->confirmKey;
                    $userModel->getAdapter()->beginTransaction();
                    try {
                        if (!$this->_auth->hasIdentity()) {
                            list($uid, $isNew) = $userModel->register($data, $confirmKey);
                            $userModel->sendInputsConfirmationMail($uid, $registerForm->getValue('kid'), $confirmKey, $isNew);
                            $this->_flashMessenger->addMessage(
                                'An email for the confirmation of your contributions has been sent to your email address.',
                                'success'
                            );
                        } else {
                            $uid = $this->_auth->getIdentity()->uid;
                            $data['uid'] = $uid;
                            $userConsultModel = new Model_User_Info();
                            $userConsultRow = $userConsultModel->fetchRow(['uid=?' => $uid, 'kid=?' => $data['kid']]);
                            if ($userConsultRow) {
                                $properties = [
                                    'age_group',
                                    'regio_pax',
                                    'cmnt_ext',
                                    'is_receiving_consultation_results',
                                    'name',
                                ];
                                foreach ($properties as $property) {
                                    if (isset($data[$property])) {
                                        $userConsultRow->{$property} = $data[$property];
                                    }
                                }
                                if (isset($data['group_specs'])) {
                                    $userConsultRow->source = is_array($data['group_specs']['source'])
                                        ? implode(',', $data['group_specs']['source'])
                                        : null;

                                    foreach (['src_misc', 'group_size', 'name_group', 'name_pers'] as $property) {
                                        if (isset($data['group_specs'][$property])) {
                                            $userConsultRow->{$property} = $data['group_specs'][$property];
                                        }
                                    }
                                }
                                $userConsultRow->save();
                            } else {
                                $userModel->addConsultationData($data);
                            }
                            unset($data['cmnt_ext']);
                            $consultationId = $data['kid'];
                            unset($data['kid']);
                            unset($data['is_contrib_under_cc']);
                            unset($data['csrf_token_register']);
                            $ageGroup = (new Model_ContributorAge())->find($data['age_group'])->current();
                            unset($data['age_group']);
                            if ($ageGroup) {
                                $data['age_group_from'] = $ageGroup['from'];
                                $data['age_group_to'] = $ageGroup['to'];
                            }

                            if (isset($data['group_specs'])) {
                                $data = array_merge($data, $data['group_specs']);
                                unset($data['group_specs']);

                                if (isset($data['source'])) {
                                    foreach ($data['source'] as $source) {
                                        $data['source'] = $source;
                                        break;
                                    }
                                }
                            }
                            $data['email'] = $this->_auth->getIdentity()->email;
                            $userModel->update($data, ['uid=?' => $uid]);
                            $inputModel = new Model_Inputs();
                            $inputsToConfirm = $inputModel->getByConfirmKey($confirmKey);

                            $qiSent = [];
                            foreach ($inputsToConfirm as $input) {
                                if (!in_array($input['qi'], $qiSent)) {
                                    $qiSent[] = $input['qi'];
                                    (new Service_Notification_InputCreatedNotification())->notify(
                                        [Service_Notification_InputCreatedNotification::PARAM_QUESTION_ID => $input['qi']]
                                    );
                                }
                            }

                            $inputModel->confirmByCkey($confirmKey, $uid);
                            (new Model_Votes_Rights())->setInitialRightsForConfirmedUser($uid, $consultationId);

                            $this->_flashMessenger->addMessage('Your inputs have been saved.', 'success');
                        }
                        $userModel->getAdapter()->commit();
                    } catch (Exception $e) {
                        $userModel->getAdapter()->rollback();
                        throw $e;
                    }
                    unset($sessInputs->confirmKey);
                    $this->redirect('/');
                } else {
                    // Reset the email value as isValid() sets it to blank since for loged in users the field is disabled
                    if ($this->_auth->hasIdentity()) {
                        $registerForm->getElement('email')->setValue($this->_auth->getIdentity()->email);
                    }
                    $this->_flashMessenger->addMessage('Please check your data!', 'error');
                }
            } else {
                if ($auth->hasIdentity()) {
                    $user = (new Model_Users())->fetchRow(
                        (new Model_Users())
                            ->select()
                            ->where('email=?', $auth->getIdentity()->email)
                    )->toArray();
                    $guessedGroupAge = (new Service_Groups())->guessGroupAge(
                        $this->_consultation,
                        $user['age_group_from'],
                        $user['age_group_to']
                    );
                    $user['age_group'] = $guessedGroupAge !== -1 ? $guessedGroupAge : null;
                    $user['is_contrib_under_cc'] = false;
                    $registerForm->populate($user);
                    $registerForm->lockEmailField();
                }
            }
            $this->view->registerForm = $registerForm;
        }

        if (!$sessInputs->confirmKey) {
            $this->_flashMessenger->addMessage(
                'There is no input to be confirmed.',
                'info'
            );
            $this->redirect('/');
        }

        Zend_Layout::getMvcInstance()->assign(
            'disableLoginMsg',
            Zend_Registry::get('Zend_Translate')->translate('Please finish contributing before logging in.')
        );
    }

    public function passwordrecoverAction()
    {
        $form = new Default_Form_PasswordRecover();
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {
                $userModel = new Model_Users();
                if ($userModel->recoverPassword($data['email'])) {
                    $this->_flashMessenger->addMessage('An e-mail that allows you to reset your password was sent to the provided address.', 'success');
                } else {
                    $this->_flashMessenger->addMessage('Password reset failed!', 'error');
                }
                $this->redirect('/');
            } else {
                $this->_flashMessenger->addMessage('Please check your data!', 'error');
            }
        }
        $this->view->form = $form;
    }

    /**
     * List Users By Group
     */
    public function userlistAction()
    {
        $kid = isset($this->_consultation->kid) ? $this->_consultation->kid : 0;
        $consultationList = array();

        if (!$this->_auth->hasIdentity()) {
            $this->_flashMessenger->addMessage('Please log in first', 'error'); // No Login
        } else {
            $identity = $this->_auth->getIdentity();
            // No Consultation ID given list consultations
            if ($kid == 0) {
                $consultationModel = new Model_Consultations();
                $groupsModel = new Model_Votes_Groups();
                $consultationList = $consultationModel->getByUserVotingRights($identity->uid)->toArray();
                foreach ($consultationList as $key => $value) {
                    $group = $groupsModel->getByConsultation($value['kid'], $identity->uid);
                    // if no group member in consultation delete consultation from array
                    if (empty($group)) {
                        unset($consultationList[$key]);
                    }
                }
                $this->view->consultationList = $consultationList;

            } elseif ($kid > 0) {
                $groupsModel = new Model_Votes_Groups();
                $consultationModel = new Model_Consultations();
                $this->view->consultationList = $consultationList;
                $group = $groupsModel->getByConsultation($kid, $identity->uid);
                // list group members
                if (count($group) > 0) {
                    $inputModel = new Model_Inputs;
                    $consultationModel = new Model_Consultations();
                    $this->view->consultation = $consultationModel->find($kid)->current();

                    $filter = [
                        [
                            'field' => 'is_votable',
                            'operator' => '=',
                            'value' => true,
                        ],
                    ];

                    $this->view->inputs = $inputModel->getCountByConsultationFiltered($kid, $filter);
                    $this->view->group = $group;
                    $this->view->identity =$identity;
                } else {
                    // its possible the user reload's the page
                    $consultationList = $consultationModel->getByUser($identity->uid)->toArray();
                    foreach ($consultationList as $key => $value) {
                        $group = $groupsModel->getByConsultation($value['kid'], $identity->uid);
                        // if no group member delete consultation from array
                        if (empty($group)) {
                            unset($consultationList[$key]);
                        }
                    }
                    // all group-member deleted ?
                    empty($consultationList) ? $this->redirect('/user/userlist/') : $this->view->consultationList = $consultationList;
                }
            }
        }

        $this->view->form = new Admin_Form_ListControl();
    }

    /**
     * Performs deny, confirm and delete actions on a single particiapnt
     */
    public function participantUpdateAction()
    {
        $form = new Admin_Form_ListControl();

        if ($form->isValid($this->getRequest()->getPost())) {
            if (!$this->_consultation['vot_to']
                || Zend_Date::now()->isEarlier(new Zend_Date($this->_consultation['vot_to'], Zend_Date::ISO_8601))
            ) {
                $votesGroupsModel = new Model_Votes_Groups();

                if ($this->getRequest()->getPost('confirm')) {
                    list($uid, $sub_uid) = explode('_', $this->getRequest()->getPost('confirm'));
                    $votesGroupsModel->confirmVoter($this->_consultation->kid, $uid, $sub_uid);
                    $this->_flashMessenger->addMessage('The voting participant was confirmed.', 'success');
                } elseif ($this->getRequest()->getPost('deny')) {
                    list($uid, $sub_uid) = explode('_', $this->getRequest()->getPost('deny'));
                    $this->_flashMessenger->addMessage('The voting participant was denied.', 'success');
                    $votesGroupsModel->denyVoter($this->_consultation->kid, $uid, $sub_uid);
                } elseif ($this->getRequest()->getPost('delete')) {
                    list($uid, $sub_uid) = explode('_', $this->getRequest()->getPost('delete'));
                    $votesGroupsModel->deleteVoter($this->_consultation->kid, $uid, $sub_uid);
                    $this->_flashMessenger->addMessage('The voting participant was deleted.', 'success');
                }
            } else {
                $this->_flashMessenger->addMessage('Voting period has ended and it is not possible to change voting results; the voting results are no longer subject to change.', 'error');
            }
        }

        $this->_redirect($this->view->url(['action' => 'userlist']), ['prependBase' => false]);
    }

    public function activityAction()
    {
        $init = 10;
        $step = 10;
        $contributionsLimit = $this->getParam('cl', $init);
        $postsLimit = $this->getParam('pl', $init);

        if (!$this->_auth->hasIdentity()) {
            $this->_flashMessenger->addMessage('Please log in.', 'error');
            $this->redirect('/');
        }

        $contributionModel = new Model_Inputs();
        $postsModel = new Model_InputDiscussion();

        $this->view->contributionsList = $contributionModel->getByUserWithDependencies(
            $this->_auth->getIdentity()->uid,
            $contributionsLimit
        );

        $this->view->postsList = $postsModel->getByUserWithDependencies(
            $this->_auth->getIdentity()->uid,
            $postsLimit
        );

        $this->view->contributionsLimit = $contributionsLimit;
        $this->view->contributionsSum = $contributionModel->getCountByUser($this->_auth->getIdentity()->uid);
        $this->view->postsLimit = $postsLimit;
        $this->view->postsSum = $postsModel->getCountByUser($this->_auth->getIdentity()->uid);
        $this->view->step = $step;
    }

    public function notificationsAction()
    {
        $form = new Admin_Form_ListControl();

        $contributionDiscussionService =
            new Service_Notification_DiscussionContributionCreatedNotification();
        $questionService = new Service_Notification_InputCreatedNotification();
        $followupService = new Service_Notification_FollowUpCreatedNotification();

        $auth = Zend_Auth::getInstance();

        if (!$auth->hasIdentity()) {
            $this->_flashMessenger->addMessage('Please log in.', 'error');
            $this->redirect('/');
        }

        if ($form->isValid($this->getRequest()->getPost())) {
            if ($this->getRequest()->getPost('unsubscribe-cd')) {
                $notificationId = $this->getRequest()->getPost('unsubscribe-cd');
                $followupService->unsubscribeById($notificationId);
                $this->_flashMessenger->addMessage('You were successfully unsubscribed.', 'success');
            } elseif ($this->getRequest()->getPost('unsubscribe-q')) {
                $notificationId = $this->getRequest()->getPost('unsubscribe-q');
                $followupService->unsubscribeById($notificationId);
                $this->_flashMessenger->addMessage('You were successfully unsubscribed.', 'success');
            } elseif ($this->getRequest()->getPost('unsubscribe-fu')) {
                $notificationId = $this->getRequest()->getPost('unsubscribe-fu');
                $followupService->unsubscribeById($notificationId);
                $this->_flashMessenger->addMessage('You were successfully unsubscribed.', 'success');
            }
        }

        $this->view->form = $form;
        $this->view->contributionDiscussion = $contributionDiscussionService->getNotifications(
            $auth->getIdentity()->uid
        );
        $this->view->questions = $questionService->getNotifications(
            $auth->getIdentity()->uid
        );
        $this->view->followups = $followupService->getNotifications(
            $auth->getIdentity()->uid
        );
    }

    public function profileAction()
    {
        $form = new Default_Form_Profile();

        $auth = Zend_Auth::getInstance();

        if (!$auth->hasIdentity()) {
            $this->_flashMessenger->addMessage('Please log in.', 'error');
            $this->redirect('/');
        }

        $userModel = new Model_Users();
        $user = $userModel->find($auth->getIdentity()->uid)->current();
        if (!$user) {
            $this->redirect('/', ['prependBase' => false]);
        }

        if ($this->_request->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $data = $form->getValues();
                try {
                    if ($userModel->updateProfile($user, $data)) {
                        $this->_flashMessenger->addMessage('Your user profile was updated.', 'success');
                        $this->redirect($this->view->url(), ['prependBase' => false]);
                    }
                } catch (Zend_Auth_Exception $e) {
                    $this->_flashMessenger->addMessage(
                        'Your current password does not match. Please try it again.',
                        'error'
                    );
                }
            } else {
                $form->populate(['email' => $user['email']]);
                $this->_flashMessenger->addMessage(
                    'Your profile cannot be updated. Please check the errors marked in the form below and try again.',
                    'error'
                );
            }
        } else {
            $form->populate($user->toArray());
            $form->populate(['email' => $user['email']]);
        }

        $this->view->form = $form;
    }
}
