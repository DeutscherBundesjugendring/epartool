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
        $this->view->countInserted = $votingRightsModel
            ->setInitialRightsByConsultation($this->_consultation->kid);
        $this->view->votingRights = $votingRightsModel
            ->getByConsultation($this->_consultation->kid);
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
            $votingRightsModel = new Model_Votes_Rights();
            $form = new Admin_Form_Voting_Rights();

            $user = $userModel->getById($uid);
            $userInfo = $userInfoModel->getLatestByUserAndConsultation($uid, $this->_consultation['kid']);
            $votingRights = $votingRightsModel
                ->getByUserAndConsultation($uid, $this->_consultation->kid);

            if ($this->_request->isPost()) {
                // form sent -> process
                $data = $this->_request->getPost();
                if ($form->isValid($data)) {
                    $votingRights->setFromArray($data)->save();
                    $this->_flashMessenger->addMessage(
                        sprintf($this->view->translate('Changes for <b>%s</b> were saved.'), $user['email']),
                        'success'
                    );
                    $this->redirect(
                        $this->view->url(array('action' => 'index', 'uid' => null)),
                        array('prependBase' => false)
                    );
                } else {
                    $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                }
            } else {
                $data = array(
                    'vt_weight' => $votingRights['vt_weight'],
                    'vt_code' => $votingRights['vt_code'],
                    'grp_siz' => $votingRights['grp_siz'],
                    'group_size_user' => $userInfo['group_size'],
                );
            }
            $form->populate($data);

            $this->view->assign(
                array('form' => $form, 'user' => $user)
            );
        } else {
            $this->_flashMessenger->addMessage('No user provided.', 'error');
            $this->redirect('/admin/voting');
        }
    }

    /**
     * List paricipants and process request to send invitation email instantly
     */
    public function invitationsAction()
    {
        $userModel = new Model_Users();
        $votingRightsModel = new Model_Votes_Rights();
        $participants = $userModel
            ->getParticipantsByConsultation($this->_consultation->kid)
            ->toArray();
        $emailList = '';
        $votingRights = [];
        foreach ($participants as $key => $value) {
            if (!empty($value['email'])) {
                $emailList .= $value['email'] . ';';
            }

            $votingRights[$value['uid']] = $votingRightsModel->getByUserAndConsultation(
                $value['uid'],
                $this->_consultation->kid
            );
            if (!empty($votingRights)) {
                $participants[$key]['votingRights'] = $votingRights[$value['uid']];
            }
        }

        $listControlForm = new Admin_Form_ListControl();
        if ($this->getRequest()->isPost() && $listControlForm->isValid($this->getRequest()->getPost())) {
            $userId = $this->getRequest()->getPost('instantSendUserId');
            if ($userId) {
                $user = (new Model_Users())->getById($userId);
                $placeholders = ['voting_url' => Zend_Registry::get('baseUrl') . '/voting/index/kid/'
                        . $this->_consultation->kid . '/authcode/' . $votingRights[$user['uid']]['vt_code']];
                if ($votingRights[$user['uid']] && $votingRights[$user['uid']]['vt_weight'] != 1) {
                    $grpSizDef = Zend_Registry::get('systemconfig')->group_size_def->toArray();
                    $templateName = Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_INVITATION_GROUP;
                    $placeholders['voting_weight'] = $votingRights[$user['uid']]['vt_weight'];
                    $placeholders['group_category'] = $grpSizDef[$votingRights[$user['uid']]['grp_siz']];
                } else {
                    $templateName = Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_INVITATION_SINGLE;
                }
                $mailer = $this->getInvitationMailer($user, $placeholders);
                $mailer
                    ->setTemplate($templateName)
                    ->addTo($user['email']);

                (new Service_Email)->queueForSend($mailer)->sendQueued();
                $this->_flashMessenger->addMessage(
                    sprintf(
                        $this->view->translate('Voting invitation to %s has been successfully sent.'),
                        $user['email']
                    ),
                    'success'
                );
                $this->redirect('/admin/voting/invitations/kid/' . $this->_consultation->kid);
            }
        }

        $appOpts = $this->getInvokeArg('bootstrap')->getOptions();

        $this->view->participants = $participants;
        $this->view->emailList = $emailList;
        $this->view->listControlForm = $listControlForm;
        $this->view->mailDefaultFrom = $appOpts['resources']['mail']['defaultFrom']['email'];
    }

    /**
     * Show voting invitation email from and send email on its submission
     */
    public function sendinvitationAction()
    {
        $uid = $this->_request->getParam('uid');
        $kid = $this->_request->getParam('kid');

        if ($uid) {
            $form = new Admin_Form_Mail_Send();
            $user = (new Model_Users())->getById($uid);
            $votingRights = (new Model_Votes_Rights())->getByUserAndConsultation(
                $user['uid'],
                $this->_consultation->kid
            );

            $placeholders = ['voting_url' => Zend_Registry::get('baseUrl') . '/voting/index/kid/'
                . $this->_consultation->kid . '/authcode/' . $votingRights['vt_code']];

            if ($votingRights && $votingRights['vt_weight'] != 1) {
                $grpSizDef = Zend_Registry::get('systemconfig')->group_size_def->toArray();
                $templateName = Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_INVITATION_GROUP;
                $placeholders['voting_weight'] = $votingRights['vt_weight'];
                $placeholders['group_category'] = $grpSizDef[$votingRights['grp_siz']];
            } else {
                $templateName = Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_INVITATION_SINGLE;
            }

            if ($this->_request->isPost()) {
                if ($form->isValid($this->_request->getPost())) {
                    $values = $form->getValues();
                    $mailer = $this->getInvitationMailer($user, $placeholders);
                    $mailer
                        ->addTo($values['mailto'])
                        ->setSubject($values['subject'])
                        ->setBodyHtml($values['body_html'])
                        ->setBodyText($values['body_text']);
                    if ($values['mailcc']) {
                        $mailer->addCc($values['mailcc']);
                    }
                    if ($values['mailbcc']) {
                        $mailer->addCc($values['mailbcc']);
                    }

                    (new Service_Email)->queueForSend($mailer)->sendQueued();
                    $this->_flashMessenger->addMessage(
                        sprintf(
                            $this->view->translate('Voting invitation to %s has been successfully sent.'),
                            $user['email']
                        ),
                        'success'
                    );
                    $this->redirect('/admin/voting/invitations/kid/' . $this->_consultation->kid);
                } else {
                    $this->_flashMessenger->addMessage(
                        'Form is not valid, please check the values entered.',
                        'error'
                    );
                }
            } else {
                $form->getElement('mailto')->setValue($user['email']);
                $form->getElement('mail_consultation')->setValue($kid);
                $form->removeElement('mail_consultation_participant');
                $form->removeElement('mail_consultation_voter');
                $form->removeElement('mail_consultation_newsletter');
                $form->removeElement('mail_consultation_followup');
                $form->populateFromTemplateName($templateName);
            }
            $this->view->form = $form;
        } else {
            $this->_flashMessenger->addMessage('No user provided.', 'error');
            $this->redirect('/admin/voting/invitations/kid/' . $this->_consultation->kid);
        }
    }

    /**
     * List voters
     */
    public function participantsAction()
    {
        $this->view->inputs = (new Model_Inputs())->getCountByConsultationFiltered(
            $this->_consultation->kid,
            [['field' => 'vot', 'operator' => '=', 'value' => 'y']]
        );
        $this->view->groups = (new Model_Votes_Groups())->getByConsultation($this->_consultation->kid);
        $this->view->form = new Admin_Form_ListControl();
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

        $mergeOptions = array(''=>'Please select…');
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
                 $messages = "";

                // get votes from both users //
                $votesIndividual = new Model_Votes_Individual();
                $subUserSelectedVotes = $votesIndividual -> getUserVotes($subUserSelected)->toArray();
                $subUserOrgVotes = $votesIndividual -> getUserVotes($subUserOrg)->toArray();

                // Votes zusammenführen
                $VotesMerged = array();

                // Array of Votes User Selected
                foreach ($subUserSelectedVotes as $key => $value) {
                    $VotesMerged[$value['tid']] = $value;
                }

                // Array of Votes User Origin (overwrite dublicate keys (tid key))
                foreach ($subUserOrgVotes as $key => $value) {
                    $VotesMerged[$value['tid']] = $value;
                }

                // Delete Votes User Origin
                if ($votesIndividual ->deleteUservotes($subUserOrg)) {
                    $messages.= 'Votes by the original user have been removed.<br />';

                    // Delete Votes User Selected
                    if ($votesIndividual->deleteUservotes($subUserSelected)) {
                        $messages .= 'Votes by selected user have been removed.<br />';
                    }

                    // Restore Votes User Origin
                    // Create vt_inp_list array()
                    $x=0;
                    $vt_inp_list = array();
                    foreach ($VotesMerged as $key => $value) {
                        $x++;
                        $votesIndividual->insertMergedUservotes($subUserOrg, $value);
                        $vt_inp_list["$x"]= $value['tid'];
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
        $votingResultsValues = $votesModel->getResultsValues($this->_consultation->kid, $qid);

        $this->view->assign($votingResultsValues);
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
        $this->_settings = $settingsModel->find($this->_consultation->kid)->current();

        if (!$this->_settings) {
            $settingsResult = $settingsModel->add($this->_consultation->kid);
            if ($settingsResult) {
                $this->_flashMessenger->addMessage('Changes saved.', 'success');
                $this->_settings = $settingsModel->find($this->_consultation->kid)->current();
            } else {
                $this->_flashMessenger->addMessage('Saving changes failed.', 'error');
            }
        }

        $form = new Admin_Form_Voting_Settings();
        $form -> setAction($this->view->baseUrl() . '/admin/voting/settings/kid/' . $this->_consultation->kid);

        $settings = array_merge($this->_settings->toArray(), $this->_consultation->toArray());
        $settings['vot_expl'] = htmlspecialchars_decode($settings['vot_expl'], ENT_COMPAT);

        $this->view->form = $form;

        $post = $this->_request->getPost();

        if ($post) {
            if (!$form->isValid($post)) {
                $this->view->form->populate($post);
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
            } else {
                $values = $this->view->form->getValues();

                $this->_settings->btn_important = $values['btn_important'];
                $this->_settings->btn_important_label = $values['btn_important_label'];
                $this->_settings->btn_numbers = $values['btn_numbers'];
                $this->_settings->btn_labels = $values['btn_labels'];
                $this->_settings->btn_important_max= $values['btn_important_max'];
                $this->_settings->save();

                $this->_flashMessenger->addMessage('Changes saved.', 'success');
            }
        } else {
            $form -> populate($settings);
        }
    }

    /**
     * Returns preconfigured Dbjr_Mail object
     * @param  array  $user          The user data array
     * @param  array  $placeholders  Array holding placeholder values to be used in the email
     * @return Dbjr_Mail             The mailer object
     */
    private function getInvitationMailer($user, $placeholders)
    {
        $view = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('view');

        $mailer = new Dbjr_Mail();
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
}
