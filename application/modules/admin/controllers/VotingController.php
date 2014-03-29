<?php

class Admin_VotingController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;

    protected $_consultation = null;

    /**
     * Construct
     * @return void
     */
    public function init()
    {
        // Setzen des Standardlayouts
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
     *
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
     *
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
                // form not submitted, initial request
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
     *
     */
    public function invitationsAction()
    {
        $userModel = new Model_Users();
        $votingRightsModel = new Model_Votes_Rights();
        $participants = $userModel->getParticipantsByConsultation(
            $this->_consultation->kid,
            array('u.email', 'u.name')
        );
        $emailList = '';
        foreach ($participants as $key => $value) {
            if (!empty($value['email'])) {
                $emailList.= $value['email'] . ';';
            }
            $votingRights = $votingRightsModel
                ->getByUserAndConsultation($value['uid'], $this->_consultation->kid);
            if (!empty($votingRights)) {
                $participants[$key]['votingRights'] = $votingRights;
            }
        }
        $this->view->assign(
            array(
                'participants' => $participants,
                'emailList' => $emailList,
            )
        );
    }

    /**
     * Send voting invitation by email via preview or directly
     *
     */
    public function sendinvitationAction()
    {
        $uid = $this->_request->getParam('uid', 0);
        $mode = $this->_request->getParam('mode');

        if ($uid > 0) {
            $date = new Zend_Date();
            $userModel = new Model_Users();
            $votingRightsModel = new Model_Votes_Rights();
            $emailModel = new Model_Emails();
            $emailTemplateModel = new Model_Emails_Templates();
            $form = new Admin_Form_Email_Send();
            $form->setAction($this->view->url());
            $formSent = false;
            $sentFromPreview = false;
            $systemconfig = Zend_Registry::get('systemconfig');
            $grp_siz_def = $systemconfig->group_size_def->toArray();

            // set defaults for given user:
            $user = $userModel->getById($uid);
            $votingRights = $votingRightsModel->getByUserAndConsultation($uid, $this -> _consultation -> kid);
            $receiver = $user['email'];
            $cc = '';
            $bcc = '';
            if ($votingRights['vt_weight'] != 1) {
                // group type is group
                $templateRef = 'vt_invit_group';
            } else {
                // group type is single
                $templateRef = 'vt_invit_single';
            }
            // prepare marker array
            $templateReplace = array(
                '{{USER}}' => (empty($user['name']) ? $user['email'] : $user['name']),
                '{{CNSLT_TITLE}}' => $this->_consultation['titl'],
                '{{VOTE_FROM}}' => $date->set($this->_consultation['vot_fr'])->get(Zend_Date::DATE_MEDIUM),
                '{{VOTE_TO}}' => $date->set($this->_consultation['vot_to'])->get(Zend_Date::DATE_MEDIUM),
//                 '{{SITEURL}}' => Zend_Registry::get('baseUrl') . '/voting/index/kid/'
//                     . $this -> _consultation -> kid . '/authcode/',
//                 '{{VTC}}' => $votingRights['vt_code'],
                '{{VOTINGURL}}' => Zend_Registry::get('baseUrl') . '/voting/index/kid/'
                    . $this->_consultation->kid . '/authcode/' . $votingRights['vt_code'],
                '{{GROUP_CATEGORY}}' => $grp_siz_def[$votingRights['grp_siz']],
                '{{VOTING_WEIGHT}}' => $votingRights['vt_weight'],
            );
            $templateRow = $emailTemplateModel->getByName($templateRef);
            $subject = '';
            $message = '';
            if (!empty($templateRow)) {
                // work with email template
                $subject = $templateRow->subj;
                $message = $templateRow->txt;
                // replace markers
                foreach ($templateReplace as $search => $replace) {
                    $subject = str_replace($search, $replace, $subject);
                    $message = str_replace($search, $replace, $message);
                }
                // use head-Area
                if ($templateRow->head=='y') {
                    $templateHeader = $emailTemplateModel->getByName('header');
                    // if head-template exists
                    if ($templateHeader) {
                        $message = $templateHeader->txt . $message;
                    }
                }
                // use footer-Area
                if ($templateRow->foot=='y') {
                    $templateFooter = $emailTemplateModel->getByName('footer');
                    // if head-template exists
                    if ($templateFooter) {
                        $message.= $templateFooter->txt;
                    }
                }
            } else {
                // no template found, give chance to write email manually
                $mode = 'preview';
                $this->_flashMessenger->addMessage('Keine E-Mail-Vorlage gefunden!', 'error');
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

            switch ($mode) {
                case 'instantsend':
                    // send mail directly
                    if ($sentFromPreview) {
                        // use data from preview
                        $mailData = $form->getValues();
                        $receiver = $mailData['empfaenger'];
                        $cc = $mailData['mailcc'];
                        $bcc = $mailData['mailbcc'];
                        $subject = $mailData['subject'];
                        $message = $mailData['message'];
                        $templateRef = null;
                        $templateReplace = null;
                    } else {
                        // use user defaults, see above
                    }

                    $sent = $emailModel->send($receiver, $subject, $message, $templateRef, $templateReplace, null, null, $cc, $bcc);
                    // redirect to overview
                    if ($sent) {
                        $this->_flashMessenger->addMessage('Votingeinladung an <b>' . $receiver . '</b> versendet.', 'success');
                    } else {
                        $this->_flashMessenger->addMessage('Beim Senden der E-Mail ist ein Fehler aufgetreten.', 'error');
                    }
                    $this->redirect('/admin/voting/invitations/kid/' . $this -> _consultation -> kid);
                    break;

                case 'preview':
                default:
                    if (!$formSent) {
                        // show form for the first time, fill with default data (see above)
                        $form->getElement('empfaenger')->setValue($receiver);
                        $form->getElement('subject')->setValue($subject);
                        $form->getElement('message')->setValue($message);
                    }
                    // assign view variables and render view script for this action
                    $this->view->form = $form;
                    break;
            }

        } else {
            $this->_flashMessenger->addMessage('Kein_e Nutzer_in angegeben!', 'error');
            $this->redirect('/admin/voting/invitations/kid/' . $this -> _consultation -> kid);
        }
    }

    /**
     * List voters
     *
     */
    public function participantsAction()
    {
        $groupsModel = new Model_Votes_Groups();

        $this->view->groups = $groupsModel->getByConsultation($this -> _consultation -> kid);
    }

    /**
     * Deny voter
     *
     */
    public function participantdenyAction()
    {
        $uid = $this->_request->getParam('uid', 0);
        $sub_uid = $this->_request->getParam('sub_uid', 0);
        $votesGroupsModel = new Model_Votes_Groups();

        if ($votesGroupsModel->denyVoter($this -> _consultation -> kid, $uid, $sub_uid)) {
            $this->_flashMessenger->addMessage('Teilnehmer_in wurde abgelehnt.', 'success');
        } else {
            $this->_flashMessenger->addMessage('Ablehnen fehlgeschlagen.', 'error');
        }

        $this->redirect('/admin/voting/participants/kid/' . $this -> _consultation -> kid);
    }

    /**
     * Confirm voter
     *
     */
    public function participantconfirmAction()
    {
        $uid = $this->_request->getParam('uid', 0);
        $sub_uid = $this->_request->getParam('sub_uid', 0);
        $votesGroupsModel = new Model_Votes_Groups();

        if ($votesGroupsModel->confirmVoter($this->_consultation->kid, $uid, $sub_uid)) {
            $this->_flashMessenger->addMessage('Teilnehmer_in wurde bestätigt.', 'success');
        } else {
            $this->_flashMessenger->addMessage('Bestätigen fehlgeschlagen.', 'error');
        }

        $this->redirect('/admin/voting/participants/kid/' . $this->_consultation->kid);
    }

    /**
     * Delete voter
     *
     */
    public function participantdeleteAction()
    {
        $uid = $this->_request->getParam('uid', 0);
        $sub_uid = $this->_request->getParam('sub_uid', 0);
        $votesGroupsModel = new Model_Votes_Groups();

        if ($votesGroupsModel->deleteVoter($this->_consultation->kid, $uid, $sub_uid) > 0) {
            $this->_flashMessenger->addMessage('Teilnehmer_in wurde gelöscht.', 'success');
        } else {
            $this->_flashMessenger->addMessage('Löschen fehlgeschlagen.', 'error');
        }

        $this->redirect('/admin/voting/participants/kid/' . $this->_consultation->kid);
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

                $this->_consultation->vot_fr = $values['vot_fr'];
                $this->_consultation->vot_to = $values['vot_to'];
                $this->_consultation->vot_show = $values['vot_show'];
                $this->_consultation->vot_res_show = $values['vot_res_show'];
                $this->_consultation->vot_expl = $values['vot_expl'];

                $this->_consultation->save();

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
