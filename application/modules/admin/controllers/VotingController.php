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
            $this->_flashMessenger->addMessage('Keine Konsultation angegeben!', 'error');
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
                        'Änderungen für <b>' . $user['email'] . '</b> gespeichert.',
                        'success'
                    );
                    $this->redirect(
                        $this->view->url(array('action' => 'index', 'uid' => null)),
                        array('prependBase' => false)
                    );
                } else {
                    $this->_flashMessenger->addMessage('Bitte überprüfe die Eingaben!', 'error');
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
            $this->_flashMessenger->addMessage('Keine User-ID angegeben!', 'error');
            $this->redirect('/admin/voting');
        }
    }

    /**
     * List paricipants for invitation
     */
    public function invitationsAction()
    {
        $userModel = new Model_Users();
        $votingRightsModel = new Model_Votes_Rights();
        $userTblName = $userModel->getName();
        $participants = $userModel
            ->getParticipantsByConsultation($this->_consultation->kid)
            ->toArray();
        $emailList = '';
        foreach ($participants as $key => $value) {
            if (!empty($value['email'])) {
                $emailList.= $value['email'] . ';';
            }

            $votingRights = $votingRightsModel->getByUserAndConsultation(
                $value['uid'],
                $this->_consultation->kid
            );
            if (!empty($votingRights)) {
                $participants[$key]['votingRights'] = $votingRights;
            }
        }

        $appOpts = $this->getInvokeArg('bootstrap')->getOptions();
        $this->view->assign(
            array(
                'participants' => $participants,
                'emailList' => $emailList,
                'mailDefaultFrom' => $appOpts['resources']['mail']['defaultFrom']['email'],
            )
        );
    }

    /**
     * Send voting invitation by email via preview or directly
     */
    public function sendinvitationAction()
    {
        $uid = $this->_request->getParam('uid', 0);
        $mode = $this->_request->getParam('mode');

        if ($uid > 0) {
            $form = new Admin_Form_Mail_Send();
            $form->setAction($this->view->url());
            $formSent = false;
            $sentFromPreview = false;
            $grp_siz_def = Zend_Registry::get('systemconfig')->group_size_def->toArray();

            $userModel = new Model_Users();
            $user = $userModel->getById($uid);

            $votingRightsModel = new Model_Votes_Rights();
            $votingRights = $votingRightsModel->getByUserAndConsultation($uid, $this -> _consultation -> kid);

            if ($votingRights && $votingRights['vt_weight'] != 1) {
                // group type is group
                $templateName = Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_INVITATION_GROUP;
                $placeholders = array(
                    'voting_weight' => $votingRights['vt_weight'],
                    'group_category' => $grp_siz_def[$votingRights['grp_siz']],
                );
            } else {
                // group type is single
                $templateName = Model_Mail_Template::SYSTEM_TEMPLATE_VOTING_INVITATION_SINGLE;
                $placeholders = array();
            }

            // check if form from preview is submitted
            if ($this->_request->isPost()) {
                $formSent = true;
                // sent from preview
                $data = $this->_request->getPost();
                if ($form->isValid($data)) {
                    // mail can be sent directly
                    $mode = 'instantsend';
                    $sentFromPreview = true;
                } else {
                    $this->_flashMessenger->addMessage('Bitte überprüfe die Eingaben!', 'error');
                    $form->populate($data);
                }
            }

            $mailer = new Dbjr_Mail();
            if ($mode === 'instantsend') {
                if ($sentFromPreview) {
                    $values = $form->getValues();
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
                } else {
                    $date = new Zend_Date();
                    $mailer
                        ->setTemplate($templateName)
                        ->setPlaceholders(
                            array_merge(
                                $placeholders,
                                array(
                                    'to_name' => empty($user['name']) ? $user['email'] : $user['name'],
                                    'to_email' => $user['email'],
                                    'consultation_title_long' => $this->_consultation['titl'],
                                    'consultation_title_short' => $this->_consultation['titl_short'],
                                    'voting_phase_start' => $date->set($this->_consultation['vot_fr'])->get(Zend_Date::DATE_MEDIUM),
                                    'voting_phase_end' => $date->set($this->_consultation['vot_to'])->get(Zend_Date::DATE_MEDIUM),
                                    'voting_url' => Zend_Registry::get('baseUrl') . '/voting/index/kid/'
                                        . $this->_consultation->kid . '/authcode/' . $votingRights['vt_code'],
                                )
                            )
                        )
                        ->addTo($user['email']);
                }
                (new Service_Email)->queueForSend($mailer);

                $this->_flashMessenger->addMessage('Votingeinladung an <b>' . $user['email'] . '</b> versendet.', 'success');
                $this->redirect('/admin/voting/invitations/kid/' . $this -> _consultation -> kid);
            } else {
                if (!$formSent) {
                    $templateModel = new Model_Mail_Template();
                    $template = $templateModel->fetchRow(
                        $templateModel->select()->where('name=?', $templateName)
                    );
                    $form->getElement('mailto')->setValue($user['email']);
                    $form->getElement('subject')->setValue($template->subject);
                    $form->getElement('body_html')->setValue($template->body_html);
                    $form->getElement('body_text')->setValue($template->body_text);
                }

                $this->view->form = $form;
            }
        } else {
            $this->_flashMessenger->addMessage('Kein_e Nutzer_in angegeben!', 'error');
            $this->redirect('/admin/voting/invitations/kid/' . $this -> _consultation -> kid);
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
        }

        $this->_redirect($this->view->url(['action' => 'participants']));
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
        $form = new Admin_Form_Voting_Participantedit();
        $form -> setAction(
            $this->view->baseUrl() . '/admin/voting/participantedit/kid/' . $this->_consultation->kid . '/uid/' . $uid . '/sub_uid/' . $sub_uid
        );
        $groupsModel = new Model_Votes_Groups();
        $participants= $groupsModel->getUserByConsultation($this->_consultation->kid);

        $mergeOptions = array(''=>'Bitte auswählen');
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
                    $messages.= 'Votes vom Original-Nutzer wurden gelöscht!<br />';

                    // Delete Votes User Selected
                    if ($votesIndividual->deleteUservotes($subUserSelected)) {
                        $messages .= 'Votes vom ausgewählten Nutzer wurden gelöscht!<br />';
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
                    $messages .= $x . ' Votes vom Original-Nutzer wurden wiederhergestellt!<br />';

                    // Delete Subuser User Selected
                    if ($groupsModel -> deleteVoterBySubUid($subUserSelected)) {
                        $messages .= 'Der ausgewählten Nutzer wurde gelöscht!<br />';
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
            $this->_flashMessenger->addMessage('Keine KonsultationsID vorhanden', 'error');
            $this->redirect('/admin');
        }

        $settingsModel = new Model_Votes_Settings();
        $this->_settings = $settingsModel->find($this->_consultation->kid)->current();

        if (!$this->_settings) {
            $settingsResult = $settingsModel->add($this->_consultation->kid);
            if ($settingsResult) {
                $this->_flashMessenger->addMessage('Votingsettings wurden angelegt', 'success');
                $this->_settings = $settingsModel->find($this->_consultation->kid)->current();
            } else {
                $this->_flashMessenger->addMessage('Fehler beim Speichen der Settings', 'error');
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
                $this->_flashMessenger->addMessage('Bitte prüfe die Formulareingaben!', 'error');
            } else {
                $values = $this->view->form->getValues();

                $this->_settings->btn_important = $values['btn_important'];
                $this->_settings->btn_important_label = $values['btn_important_label'];
                $this->_settings->btn_numbers = $values['btn_numbers'];
                $this->_settings->btn_labels = $values['btn_labels'];
                $this->_settings->btn_important_max= $values['btn_important_max'];
                $this->_settings->save();

                $this->_flashMessenger->addMessage('Die Änderungen wurden gespeichert', 'success');
            }
        } else {
            $form -> populate($settings);
        }
    }
}
