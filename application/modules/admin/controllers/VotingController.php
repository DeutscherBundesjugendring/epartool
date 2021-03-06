<?php

class Admin_VotingController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_consultation = null;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $kid = $this->_request->getParam('kid', 0);
        if ($kid > 0) {
            $consultationModel = new Model_Consultations();
            $this->_consultation = $consultationModel->find($kid)->current();
            $this->view->consultation = $this->_consultation;
        } else {
            $this->_flashMessenger->addMessage('No consultation provided.', 'error');
            $this->redirect('/admin');
        }
    }

    /**
     * List Voting Rights
     */
    public function indexAction()
    {
        $votingRightsModel = new Model_Votes_Rights();

        $listControlForm = new Admin_Form_ListControl();
        if ($this->getRequest()->isPost() && $listControlForm->isValid($this->getRequest()->getPost())) {
            $userId = $this->getRequest()->getPost('instantSendUserId');
            if ($userId) {
                $user = (new Model_Users())->getById($userId);
                $votingRights = $votingRightsModel->find($this->_consultation->kid, $user['uid'])->current();
                if ($votingRights && $votingRights['vt_weight'] != 1) {
                    $templateName = Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_INVITATION_GROUP;
                } else {
                    $templateName = Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_INVITATION_SINGLE;
                }
                $mailer = $this->getInvitationMailer($user, $votingRights, $templateName);
                $mailer->addTo($user['email']);

                (new Service_Email)->queueForSend($mailer)->sendQueued();
                (new Model_User_Info())->update(
                    ['invitation_sent_date' => new Zend_Db_Expr('NOW()')],
                    ['uid = ?' => $user['uid'], 'kid' => $this->_consultation['kid']]
                );
                $this->_flashMessenger->addMessage(
                    sprintf(
                        $this->view->translate('Voting invitation to %s has been successfully sent.'),
                        $user['email']
                    ),
                    'success'
                );
                $this->redirect(
                    $this->view->url(['action' => 'index', 'kid' => $this->_consultation->kid]),
                    ['prependBase' => false]
                );
            }
        }

        $appOpts = $this->getInvokeArg('bootstrap')->getOptions();

        $this->view->groupSizes = (new Model_GroupSize())->getOptionsByConsultation($this->_consultation['kid']);
        $this->view->listControlForm = $listControlForm;
        $this->view->mailDefaultFrom = $appOpts['resources']['mail']['defaultFrom']['email'];
        $this->view->votingRights = (new Model_Votes_Rights())->getByConsultation($this->_consultation->kid);
    }

    /**
     * Edit Voting Rights
     */
    public function editrightsAction()
    {
        $uid = $this->_request->getParam('uid', 0);
        if ($uid > 0) {
            $userModel = new Model_Users();
            $userInfoModel = new Model_User_Info();
            $form = new Admin_Form_Voting_Rights($this->_consultation);

            $user = $userModel->getById($uid);
            $userInfo = $userInfoModel->getLatestByUserAndConsultation($uid, $this->_consultation['kid']);
            $votingRights = (new Model_Votes_Rights())->find($this->_consultation->kid, $uid)->current();

            if ($this->_request->isPost()) {
                $data = $this->_request->getPost();
                if ($form->isValid($data)) {
                    $votingRights->setFromArray($data)->save();
                    $this->_flashMessenger->addMessage(
                        sprintf($this->view->translate('Changes for <b>%s</b> were saved.'), $user['email']),
                        'success'
                    );
                    $this->redirect($this->view->url(['action' => 'index', 'uid' => null]), ['prependBase' => false]);
                } else {
                    $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                }
            } else {
                $data = [
                    'vt_weight' => $votingRights['vt_weight'],
                    'vt_code' => $votingRights['vt_code'],
                    'grp_siz' => $votingRights['grp_siz'],
                    'group_size_user' => $userInfo['group_size_user'],
                ];
            }
            $form->populate($data);

            $this->view->form = $form;
            $this->view->user = $user;
        } else {
            $this->_flashMessenger->addMessage('No user provided.', 'error');
            $this->redirect('/admin/voting');
        }
    }

    public function createRightsAction()
    {
        $userModel = new Model_Users();
        $users = $userModel->getWithoutVotingRights($this->_consultation->kid);

        $form = new Admin_Form_Voting_RightsAdd($this->_consultation, $users);
        $votingRights = new Model_Votes_Rights();

        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            $data['kid'] = $this->_consultation->kid;
            if ($form->isValid($data)) {
                $votingRights->addPermission($data);
                $this->_flashMessenger->addMessage(
                    $this->view->translate('The voting permission was created.'),
                    'success'
                );
                $this->redirect($this->view->url(['action' => 'index']), ['prependBase' => false]);
            } else {
                $this->_flashMessenger->addMessage(
                    'New voting permission cannot be created. Please check the errors marked in the form below and try again.',
                    'error'
                );
            }
        } else {
            $form->populate(['vt_code' => $votingRights->generateVotingCode()]);
        }

        $this->view->usersWithoutRights = count ($users);
        $this->view->form = $form;
    }

    /**
     * Show voting invitation email from and send email on its submission
     */
    public function sendinvitationAction()
    {
        $uid = $this->_request->getParam('uid');
        $kid = $this->_consultation->kid;

        if (!$uid) {
            $this->_flashMessenger->addMessage('No user provided.', 'error');
            if (!$kid) {
                $this->redirect($this->view->url(['action' => 'index']), ['prependBase' => false]);
            }
            $this->redirect($this->view->url(['action' => 'index', 'kid' => $kid]), ['prependBase' => false]);
        }

        $form = new Admin_Form_Mail_Send();
        $form->removeElement('mail_consultation');
        $form->removeElement('mail_consultation_participant');
        $form->removeElement('mail_consultation_voter');
        $form->removeElement('mail_consultation_newsletter');
        $form->removeElement('mail_consultation_followup');

        $user = (new Model_Users())->getById($uid);
        $votingRights = (new Model_Votes_Rights())->find($kid, $user['uid'])->current();
        $templateName = $votingRights && $votingRights['vt_weight'] != 1
            ? Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_INVITATION_GROUP
            : Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_INVITATION_SINGLE;
        $mailer = $this->getInvitationMailer($user, $votingRights, $templateName);

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $values = $form->getValues();
                $mailer
                    ->addTo($values['mailto'])
                    ->clearSubject()
                    ->setSubject($values['subject'])
                    ->setBodyHtml($values['body_html'])
                    ->setBodyText($values['body_text']);
                if ($values['mailcc']) {
                    $mailer->addCc($values['mailcc']);
                }
                if ($values['mailbcc']) {
                    $mailer->addBcc($values['mailbcc']);
                }

                (new Service_Email)->queueForSend($mailer)->sendQueued();
                (new Model_User_Info())->update(
                    ['invitation_sent_date' => new Zend_Db_Expr('NOW()')],
                    ['uid = ?' => $user['uid'], 'kid' => $this->_consultation['kid']]
                );
                $this->_flashMessenger->addMessage(
                    sprintf(
                        $this->view->translate('Voting invitation to %s has been successfully sent.'),
                        $user['email']
                    ),
                    'success'
                );
                $this->redirect($this->view->url(['action' => 'index', 'kid' => $kid]), ['prependBase' => false]);
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
            }
        } else {
            $mailer->addTo($user['email']);
            $form->getElement('mailto')->setValue($user['email']);
            $mailerData = $mailer->getEmailData();
            $form->getElement('subject')->setValue($mailerData['subject']);
            $form->getElement('body_html')->setValue($mailerData['body_html']);
            $form->getElement('body_text')->setValue($mailerData['body_text']);
        }

        $this->view->form = $form;
        $this->view->components = (new Model_Mail_Component())->fetchAll();
        $this->view->placeholders = (new Model_Mail_Placeholder())->getByTemplateName($templateName);
    }

    /**
     * List voters
     */
    public function participantsAction()
    {
        $this->view->inputs = (new Model_Inputs())->getCountByConsultationFiltered(
            $this->_consultation->kid,
            [['field' => 'is_votable', 'operator' => '=', 'value' => true]]
        );
        $this->view->groups = (new Model_Votes_Groups())->getByConsultation($this->_consultation->kid);
        $this->view->form = new Admin_Form_ListControl();
        $this->view->csrfToken = (new Service_Token())->get();
        $this->view->jsTranslations = $this->getJsTranslations();
    }

    /**
     * Performs deny, confirm and delete actions on a single particiapnt
     */
    public function participantUpdateAction()
    {
        $form = new Admin_Form_ListControl();

        if ($form->isValid($this->getRequest()->getPost())) {
            $votesGroupsModel = new Model_Votes_Groups();

            if ($this->getRequest()->getPost('confirm')) {
                list($uid, $sub_uid) = explode('_', $this->getRequest()->getPost('confirm'));
                $votesGroupsModel->confirmVoter($this->_consultation->kid, $uid, $sub_uid);
                $this->_flashMessenger->addMessage('Voting participant has been confirmed.', 'success');
            } elseif ($this->getRequest()->getPost('deny')) {
                list($uid, $sub_uid) = explode('_', $this->getRequest()->getPost('deny'));
                $this->_flashMessenger->addMessage('Voting participant has been denied.', 'success');
                $votesGroupsModel->denyVoter($this->_consultation->kid, $uid, $sub_uid);
            } elseif ($this->getRequest()->getPost('delete')) {
                list($uid, $sub_uid) = explode('_', $this->getRequest()->getPost('delete'));
                $votesGroupsModel->deleteVoter($this->_consultation->kid, $uid, $sub_uid);
                $this->_flashMessenger->addMessage('Voting participant has been deleted.', 'success');
            } elseif ($this->getRequest()->getPost('reminderVoter')) {
                (new Service_VotingReminder)->sendToVoter($this->getRequest()->getPost('reminderVoter'));
                $this->_flashMessenger->addMessage('Reminder to Voter has been sent.', 'success');
            } elseif ($this->getRequest()->getPost('reminderGroupLeader')) {
                list($uid, $sub_uid, $kid) = explode('_', $this->getRequest()->getPost('reminderGroupLeader'));
                (new Service_VotingReminder)->sendToGroupLeader($uid, $sub_uid, $kid);
                $this->_flashMessenger->addMessage('Reminder to Group leader has been sent.', 'success');
            }
        }

        $this->_redirect($this->view->url(['action' => 'participants']), ['prependBase' => false]);
    }

    /**
     * Merge two participants
     */
    public function participanteditAction()
    {
        $sub_uid = $this->_request->getParam('sub_uid', 0);
        $uid = $this->_request->getParam('uid', 0);
        $kid = $this->_request->getParam('kid', 0);

        // Create Form
        $form = new Admin_Form_Voting_Participantedit($kid);
        $form -> setAction(
            $this->view->baseUrl() . '/admin/voting/participantedit/kid/' . $this->_consultation->kid . '/uid/' . $uid . '/sub_uid/' . $sub_uid
        );
        $groupsModel = new Model_Votes_Groups();
        $participants= $groupsModel->getUserByConsultation($this->_consultation->kid);

        $mergeOptions = [''=>'Please select…'];
        foreach ($participants as $user) {
            if ($sub_uid!=$user['sub_uid']) {
                $mergeOptions[$user['sub_uid']] = $user['sub_user'];
            } else {
                $this->view->user = $user['sub_user'];
            }
        }
        $form->getElement('merge')->setMultioptions($mergeOptions);
        $this->view->form = $form;
        // End Create Form

        // REQUEST_METHOD POST
        $post = $this->_request->getPost();
        if ($post) {
            if (!$form->isValid($post)) {
                $this->view->form->populate($post);
            } else {
                 $values = $this->view->form->getValues();
                 $subUserSelected =  $values['merge'];
                 $subUserOrg = $sub_uid;
                 $messages = '';

                // get votes from both users
                $votesIndividual = new Model_Votes_Individual();
                $subUserSelectedVotes = $votesIndividual -> getUserVotes($subUserSelected)->toArray();
                $subUserOrgVotes = $votesIndividual -> getUserVotes($subUserOrg)->toArray();
                $votesMerged = [];

                // Array of Votes User Selected
                foreach ($subUserSelectedVotes as $key => $value) {
                    $votesMerged[$value['tid']] = $value;
                }

                // Array of Votes User Origin (overwrite duplicate keys (tid key))
                foreach ($subUserOrgVotes as $key => $value) {
                    $votesMerged[$value['tid']] = $value;
                }

                // Delete Votes User Origin
                if ($votesIndividual ->deleteUservotes($subUserOrg)) {
                    $messages .= 'Votes by the original user have been removed.<br />';

                    // Delete Votes User Selected
                    if ($votesIndividual->deleteUservotes($subUserSelected)) {
                        $messages .= 'Votes by selected user have been removed.<br />';
                    }

                    // Restore Votes User Origin
                    // Create vt_inp_list array()
                    $x = 0;
                    $vt_inp_list = [];
                    foreach ($votesMerged as $key => $value) {
                        $x++;
                        $votesIndividual->insertMergedUservotes($subUserOrg, $value);
                        $vt_inp_list["$x"] = $value['tid'];
                    }
                    $messages .= $x . ' Votes by the original user have been restored.<br />';

                    // Delete Subuser User Selected
                    if ($groupsModel -> deleteVoterBySubUid($subUserSelected)) {
                        $messages .= 'User has been deleted.';
                    }

                    $this->_flashMessenger->addMessage($messages, 'success');
                    $this->redirect('/admin/voting/participants/kid/' . $this->_consultation->kid);
                }
            }
        }
    }

    public function resultsAction()
    {
        $qid = $this->_request->getParam('qid', 0);
        $votesModel = new Model_Votes();

        $this->view->assign($votesModel->getResultsValues($this->_consultation->kid, $qid));
    }

    public function downloadExcelAction()
    {
        $questionId = $this->_request->getParam('questionId', 0);
        if ($questionId === 0) {
            $this->_flashMessenger->addMessage('No question was selected.', 'error');
            $this->redirect('/admin/voting/results/kid/' . $this->_consultation['kid']);
        }

        $spreadsheet = (new Service_VotingResultsExport())->exportResults($this->_consultation, $questionId);
        if (!$spreadsheet) {
            $this->_flashMessenger->addMessage('There are no voting results to export.', 'error');
            $this->redirect('/admin/voting/results/kid/' . $this->_consultation['kid']);
        }
        $fileName = $this->_consultation['titl_short'] . ' ' . $questionId . '.ods';

        // Redirect output to a client’s web browser (OpenDocument)
        header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Ods');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Voting settings
     */
    public function settingsAction()
    {
        $validator = new Zend_Validate_Int();
        if (!$validator->isValid($this->_consultation->kid)) {
            $this->_flashMessenger->addMessage('No consultation provided.', 'error');
            $this->redirect('/admin');
        }

        $settingsModel = new Model_Votes_Settings();
        $settings = $settingsModel->find($this->_consultation->kid)->current();

        if (!$settings) {
            $settingsResult = $settingsModel->add($this->_consultation->kid);
            if ($settingsResult) {
                $this->_flashMessenger->addMessage('Changes saved.', 'success');
                $settings = $settingsModel->find($this->_consultation->kid)->current();
            } else {
                $this->_flashMessenger->addMessage('Saving changes failed.', 'error');
            }
        }

        $votingButtonSetModel = new Model_VotingButtonSet();
        $votingButtonSetSettings = $votingButtonSetModel->getSet($this->_consultation->kid);

        $form = new Admin_Form_Voting_Settings();
        $form->setAction($this->view->baseUrl() . '/admin/voting/settings/kid/' . $this->_consultation->kid);
        $post = $this->_request->getPost();
        if ($post) {
            if (!$form->isValid($post)) {
                $form->populate(array_merge($post, $settings->toArray()));
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
            } else {
                $values = $form->getValues();
                $valid = true;

                $votingService = new Service_Voting();
                try {
                    $newVotingButtonSetSettings = $votingService
                        ->prepareButtonSets($this->_consultation->kid, $values['buttonSets']);
                } catch(Service_Exception_InvalidButtonSetException $e) {
                    $this->_flashMessenger->addMessage('Voting button set settings is invalid. Every set must have at least two buttons enabled and every mandatory button must be enabled.', 'error');
                    $valid = false;
                }

                if ((new Model_Votes_Individual())->getCountByConsultation($this->_consultation->kid)) {
                    if ((int) $settings->is_btn_important !== (int) $values['is_btn_important']) {
                        $values['is_btn_important'] = $settings->is_btn_important;
                        $this->_flashMessenger->addMessage('Voting has started already. You cannot disable the Superbutton.', 'error');
                        $valid = false;
                    }
                    if ((int) $settings->btn_no_opinion !== (int) $values['btn_no_opinion']) {
                        $values['btn_no_opinion'] = $settings->btn_no_opinion;
                        $this->_flashMessenger->addMessage('Voting has started already. You cannot disable No opinion button.', 'error');
                        $valid = false;
                    }
                    if (isset($values['btn_important_max'])
                        && (int) $settings->btn_important_max > (int) $values['btn_important_max']
                    ) {
                        $values['btn_important_max'] = $settings->btn_important_max;
                        $this->_flashMessenger->addMessage('Voting has started already. You cannot restrict using of superbutton.', 'error');
                        $valid = false;
                    }
                    if (isset($values['btn_important_factor'])
                        && (int) $settings->btn_important_factor !== (int) $values['btn_important_factor']
                    ) {
                        $values['btn_important_factor'] = $settings->btn_important_factor;
                        $this->_flashMessenger->addMessage('Voting has started already. You cannot change rating factor.', 'error');
                        $valid = false;
                    }
                    if (!$votingService->areButtonSetsCompatible(
                        $values['button_type'],
                        $settings->button_type,
                        $values['buttonSets']
                    )) {
                        $values['button_type'] = $settings->button_type;
                        $this->_flashMessenger->addMessage('Voting has started already. You cannot change button type to incompatible type.', 'error');
                        $valid = false;
                    }
                }

                if ($valid) {
                    $dbAdapter = $votingButtonSetModel->getAdapter();
                    $dbAdapter->beginTransaction();
                    $votingButtonSetModel->removeSet($this->_consultation->kid);
                    foreach ($newVotingButtonSetSettings as $row) {
                        $votingButtonSetModel->insert($row);
                    }
                    $dbAdapter->commit();

                    $settings->is_btn_important = $values['is_btn_important'];
                    $settings->btn_no_opinion = $values['btn_no_opinion'];
                    $settings->button_type = $values['button_type'];
                    if (isset($values['btn_important_label'])) {
                        $settings->btn_important_label = $values['btn_important_label'];
                    }
                    if (isset($values['btn_important_max'])) {
                        $settings->btn_important_max = $values['btn_important_max'];
                    }
                    if (isset($values['btn_important_factor'])) {
                        $settings->btn_important_factor = $values['btn_important_factor'];
                    }
                    $settings->save();
                    $this->_flashMessenger->addMessage('Changes saved.', 'success');
                }

                $form->populate($settings->toArray());
            }
        } else {
            $form->populate(array_merge($settings->toArray(), $votingButtonSetSettings));
        }

        $this->view->form = $form;
        $this->view->jsTranslations = $this->getJsTranslations();
    }

    /**
     * @param array $user
     * @param \Zend_Db_Table_Row $votingRights
     * @param string $templateName
     * @return \Dbjr_Mail
     * @throws \Zend_Exception
     * @throws \Zend_Mail_Exception
     */
    private function getInvitationMailer($user, Zend_Db_Table_Row $votingRights, $templateName)
    {
        $placeholders = [
            'voting_url' => vsprintf(
                '%s/voting/index/kid/%s/authcode/%s',
                [Zend_Registry::get('baseUrl'), $this->_consultation->kid, $votingRights['vt_code']]
            ),
        ];

        if ($votingRights && $votingRights['vt_weight'] != 1) {
            $grpSizDef = (new Model_GroupSize())->getOptionsByConsultation($this->_consultation['kid']);
            $placeholders['group_category'] = $grpSizDef[$votingRights['grp_siz']];
            $placeholders['voting_weight'] = $votingRights['vt_weight'];
        }

        $view = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('view');

        $templateModel = new Model_Mail_Template();
        $template = $templateModel->fetchRow(
            $templateModel->select()->where('name=?', $templateName)
        );

        $mailer = new Dbjr_Mail();
        $mailer->setSubject($template->subject);
        $mailer->setBodyHtml($template->body_html);
        $mailer->setBodyText($template->body_text);
        $mailer->setPlaceholders(
            array_merge(
                $placeholders,
                [
                    'to_name' => empty($user['name']) ? $user['email'] : $user['name'],
                    'to_email' => $user['email'],
                    'consultation_title_long' => $this->_consultation['titl'],
                    'consultation_title_short' => $this->_consultation['titl_short'],
                    'voting_phase_start' => $view->formatDate(
                        $this->_consultation['vot_fr'],
                        Zend_Date::DATE_MEDIUM
                    ),
                    'voting_phase_end' => $view->formatDate(
                        $this->_consultation['vot_to'],
                        Zend_Date::DATE_MEDIUM
                    ),
                ]
            )
        );

        return $mailer;
    }

    public function changeMemberAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest() || !$this->getRequest()->isPost()) {
            $response = $this->getResponse();
            $response->setHttpResponseCode(400);
            $response->sendResponse();
        }

        $token = $this->_request->getParam('token', null);
        $consultationId = $this->_request->getParam('kid', null);
        $uid = $this->_request->getParam('uid', null);
        $subUid = $this->_request->getParam('subuid', null);
        $tokenService = new Service_Token();

        if (!$tokenService->verify($token) || !$uid || !$subUid ) {
            $response = $this->getResponse();
            $response->setHttpResponseCode(403);
            $response->sendResponse();
        }

        try {
            $this->_helper->json([
                'data' => (new Service_PropertyAjaxUpdate(Zend_Registry::get('Zend_Translate')))
                    ->toggleParticipantIsMember($uid, $subUid, $consultationId),
                'token' => $tokenService->get(),
            ]);
        } catch (Service_Exception_PropertyAjaxUpdateException $e) {
            $this->getResponse()->setHttpResponseCode(400);
            $this->_helper->json([
                'error' => 'Cannot update participant',
            ]);
        }
    }

    /**
     * @return string[]
     */
    private function getJsTranslations()
    {
        return [
            'voting_button_set_at_least_two_buttons_enabled_error' => $this->view->translate(
                'At least two voting buttons in the set must be enabled'
            ),
            'entity_toggle_flag_label_unknown' => $this->view->translate('Unknown'),
            'entity_toggle_flag_label_loading' => $this->view->translate('Loading…'),
        ];
    }
}
