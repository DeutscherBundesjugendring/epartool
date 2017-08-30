<?php

class Admin_ConsultationController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_consultation = null;


    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->initView();

        $kid = $this->getRequest()->getParam('kid');
        if ($kid) {
            $consultationModel = new Model_Consultations();
            $this->_consultation = $consultationModel->find($kid)->current();
            $this->view->consultation = $this->_consultation;
        }
    }

    /**
     * @desc consultation votingprepare
     * @return void
     */
    public function indexAction()
    {
        $inputsModel = (new Model_Inputs());
        $form = new Admin_Form_ListControl();

        $db = $inputsModel->getAdapter();
        $db->beginTransaction();
        try {
            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {
                    if (isset($data['deleteInput'])) {
                        $inputsModel->deleteById($data['deleteInput']);
                        $this->_flashMessenger->addMessage('Input was deleted.', 'success');
                    } elseif (isset($data['deleteDiscContrib'])) {
                        (new Model_InputDiscussion())->delete(['id=?' => $data['deleteDiscContrib']]);
                        $this->_flashMessenger->addMessage('Discussion contribution was deleted.', 'success');
                    }
                }
            }

            $inputs = $inputsModel->getComplete(
                [
                    $inputsModel->info(Model_Inputs::NAME) . '.is_confirmed IS NULL',
                    (new Model_Questions())->info(Model_Questions::NAME) . '.kid = ?' => $this->_consultation['kid'],
                ]
            );

            $inputDiscussionModel = (new Model_InputDiscussion());
            $discussionContribs = $inputDiscussionModel->fetchAll(
                $inputDiscussionModel
                    ->select()
                    ->setIntegrityCheck(false)
                    ->from(['id' => $inputDiscussionModel->info(Model_InputDiscussion::NAME)])
                    ->join(
                        ['i' =>(new Model_Inputs())->info(Model_Inputs::NAME)],
                        'id.input_id = i.tid',
                        []
                    )
                    ->join(
                        ['q' =>(new Model_Questions())->info(Model_Questions::NAME)],
                        'q.qi = i.qi',
                        []
                    )
                    ->join(
                        ['u' => (new Model_Users())->info(Model_Users::NAME)],
                        'u.uid = id.user_id',
                        ['uid', 'name']
                    )
                    ->where('q.kid = ?', $this->_consultation['kid'])
                    ->order('time_created DESC')
            );

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

        $this->view->form = $form;
        $this->view->inputs = $inputs;
        $this->view->discussionContribs = $discussionContribs;
    }

    /**
     * create new Consultation
     */
    public function newAction()
    {
        $form = new Admin_Form_Consultation();
        $consultationModel = new Model_Consultations();

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if (!array_key_exists('proj', $postData)) {
                $postData['proj'] = [];
            }
            if (!in_array(Zend_Registry::get('systemconfig')->project, $postData['proj'])) {
                $postData['proj'][] = Zend_Registry::get('systemconfig')->project;
            }
            $mediaService = new Service_Media();

            if ($form->isValid($postData)) {
                $consultationModel->getAdapter()->beginTransaction();
                try {
                    $consultationRow = $consultationModel->createRow($postData);
                    $consultationRow->proj = implode(',', $form->getElement('proj')->getValue());

                    $newKid = $consultationRow->save();

                    if ($newKid) {
                        $mediaService->createDir($newKid);

                        $fileName = $form->getElement('img_file')->getFileName();
                        if ($fileName) {
                            $consultationRow->img_file = Service_Media::MEDIA_DIR_CONSULTATIONS . '/' .$newKid . '/'
                                . $mediaService->sanitizeFilename($fileName);
                            $mediaService->upload(
                                Dbjr_File::pathinfoUtf8($fileName, PATHINFO_BASENAME),
                                $newKid
                            );
                            $consultationRow->save();
                        }
                    } else {
                        throw new \Exception('Create consultation failed');
                    }

                    if(!(new Model_Articles())->createRow([
                        'kid' => $newKid,
                        'proj' => $consultationRow->proj,
                        'desc' => 'Info',
                        'is_showed' => true,
                        'ref_nm' => 'article_explanation',
                        'artcl' => '',
                        'sidebar' => '',
                        'parent' => null,
                        'time_modified' => date('Y-m-d H:i:s'),
                    ])->save()) {
                        throw new \Exception('Create default article page for new consultation failed');
                    }

                    (new Model_ContributorAge())->insert(['consultation_id' => $newKid, 'from' => 1]);
                    (new Model_GroupSize())->insert(['consultation_id' => $newKid, 'from' => 1, 'to' => 2]);

                    $consultationModel->getAdapter()->commit();
                } catch (\Exception $e) {
                    $consultationModel->getAdapter()->rollBack();
                    $this->_flashMessenger->addMessage('Creating new consultation failed.', 'error');
                    throw $e;
                }

                $this->_flashMessenger->addMessage('New consultation has been created.', 'success');
                $this->_redirect('/admin/consultation/edit/kid/' . $consultationRow->kid);
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                $form->populate($postData);
            }
        } else {
            $form->getElement('ord')->setValue($consultationModel->getMaxOrder() + 1);
        }

        $this->view->form = $form;
    }

    /**
     * edit Consultation settings
     */
    public function editAction()
    {
        $form = new Admin_Form_Consultation();
        $form->setKid($this->_consultation->kid);
        $form->getElement('img_file')->setIsLockDir(false);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if (!array_key_exists('proj', $postData)) {
                $postData['proj'] = [];
            }
            if (!in_array(Zend_Registry::get('systemconfig')->project, $postData['proj'])) {
                $postData['proj'][] = Zend_Registry::get('systemconfig')->project;
            }

            if ($form->isValid($postData)) {
                $this->_consultation->setFromArray($form->getValues());
                $this->_consultation->proj = implode(',', $form->getElement('proj')->getValue());
                $this->_consultation->discussion_from = $this->_consultation->discussion_from ? $this->_consultation->discussion_from : null;
                $this->_consultation->discussion_to = $this->_consultation->discussion_to ? $this->_consultation->discussion_to : null;
                $this->_consultation->save();
                $this->_flashMessenger->addMessage('Changes saved.', 'success');

                $this->_redirect('/admin/consultation/edit/kid/' . $this->_consultation->kid);
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                $form->populate($form->getValues());
            }
        } else {
            $form->populate($this->_consultation->toArray());
            $form->getElement('proj')->setValue(explode(',', $this->_consultation['proj']));
        }

        $this->view->form = $form;
    }

    /**
     * statistical Report
     */
    public function reportAction()
    {
        $kid = $this->_request->getParam('kid', 0);
        if (empty($kid)) {
            $this->_flashMessenger->addMessage('No consultation provided.', 'error');
            $this->redirect('/admin');
        }
        $inputsModel = new Model_Inputs();
        $questionModel = new Model_Questions();

        $questionRowset = $questionModel->getByConsultation($kid);
        $questions = array();
        foreach ($questionRowset as $question) {
            $question = $question->toArray();
            $questions[$question['qi']] = $question;
            $questions[$question['qi']]['nrInputsConfirmed'] = $inputsModel
                ->getCountContributionsConfirmed($inputsModel->selectCountContributionsByQuestion($question['qi']));
            $questions[$question['qi']]['nrInputsVoting'] = $inputsModel
                ->getCountContributionsVotable($inputsModel->selectCountContributionsByQuestion($question['qi']));
        }

        $votesIndivModel = new Model_Votes_Individual();
        $votesRightsModel = new Model_Votes_Rights();

        $this->view->projectSettings = (new Model_Projects())->find(Zend_Registry::get('systemconfig')->project)
            ->current()
            ->toArray();

        $this->view->assign(array(
            'nrParticipants' => $inputsModel->getCountParticipantsByConsultation($kid),
            'nrInputs' => $inputsModel->getCountContributionsByConsultation($kid),
            'nrInputsConfirmed' => $inputsModel->getCountContributionsConfirmed(
                $inputsModel->selectCountContributionsByConsultation($kid)
            ),
            'nrInputsUnconfirmed' => $inputsModel->getCountContributionsUnconfirmed(
                $inputsModel->selectCountContributionsByConsultation($kid)
            ),
            'nrInputsBlocked' => $inputsModel->getCountContributionsBlocked(
                $inputsModel->selectCountContributionsByConsultation($kid)
            ),
            'nrInputsVoting' => $inputsModel->getCountContributionsVotable(
                $inputsModel->selectCountContributionsByConsultation($kid)
            ),
            'questions' => $questions,
            'votingCountIndiv' => $votesIndivModel->getCountByConsultation($kid),
            'weightCounts' => $votesRightsModel->getWeightCountsByConsultation($kid)
        ));
    }

    /**
     * Ajax-Delete Action (no view)
     * Need param integer kid in request-object
     */
    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $return = array(
            'success'=>true,
            'message'=>'',
            'params'=>array(),
            'return'=>array()
        );

        $params = $this->getRequest()->getParams();
        $return['params'] = $params;

        // Validation userlevel
        $current_user = Zend_Auth::getInstance()->getIdentity();
        if ($current_user->role !== Model_Users::ROLE_ADMIN) {
            $return['success'] = false;
            $return['message'] = 'Consultation invalid.';
        }

        // Validation kid
        if (empty($params['kid'])) {
            $return['success'] = false;
            $return['message'] = 'Consultation invalid.';
        }

        // Validation consultation exists
        $consultationModel = new Model_Consultations();
        $consultation = $consultationModel->getById($params['kid']);
        if (!$consultation) {
            $return['success'] = false;
            $return['message'] = 'Consultation not found.';
        }

        // Validation successful
        if ($return['success']) {
            $kid = $params['kid'];

            // Delete articles by consultation
            $articleModel = new Model_Articles();
            $articles = $articleModel->getByConsultation($kid);
            if ($articles) {
                foreach ($articles as $article) {
                    $articleModel->deleteById($article['art_id']);
                }
            }

            // Delete Consultation
            $consultationModel->deleteById($kid);
            (new Service_Media())->deleteDir($kid, null, true);
        }

        $this->_redirect('/admin');
    }

    /**
     * Displays the form to edit phase names
     */
    public function phasesAction()
    {
        $form = new Admin_Form_ConsultationPhases();

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                if ($postData['enableCustomNames']) {
                    array_walk($postData, function(&$el) {$el = $el ? $el : null;});
                } else {
                    foreach ($form->getElements() as $element) {
                        $postData[$element->getName()] = null;
                    }
                }
                $this->_consultation
                    ->setFromArray($postData)
                    ->save();
                $this->_flashMessenger->addMessage('Custom phase names have been saved.', 'success');
                $this->redirect($this->view->url(), ['prependBase' => false]);
            } else {
                $form->setActive();
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
            }
        } elseif ($this->_consultation->phase_info
            || $this->_consultation->phase_support
            || $this->_consultation->phase_input
            || $this->_consultation->phase_voting
            || $this->_consultation->phase_followup
        ) {
            $form->setActive();
            $form->populate($this->_consultation->toArray());
        }

        $this->view->form = $form;
    }

    public function contributionSubmissionFormAction()
    {
        $consultationId = $this->_request->getParam('kid', 0);
        if (empty($consultationId)) {
            $this->_flashMessenger->addMessage('No consultation provided.', 'error');
            $this->redirect('/admin');
        }

        $consultationModel = new Model_Consultations();

        $registrationForm = new Admin_Form_ContributionSubmission();
        $anonymousForm = new Admin_Form_AnonymousContributionSubmission();
        $anonymousSwitch = (bool) $this->_consultation['anonymous_contribution'];

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $anonymousSwitch = isset($formData['submitAnonymous']);
            if (isset($formData['submit'])) {
                if ($registrationForm->isValid($formData)) {
                    $db = $consultationModel->getAdapter();
                    $db->beginTransaction();
                    try {
                        $data = [];
                        foreach (['field_switch_name',
                                     'field_switch_age',
                                     'field_switch_state',
                                     'field_switch_comments',
                                     'allow_groups',
                                     'field_switch_contribution_origin',
                                     'field_switch_individuals_sum',
                                     'field_switch_group_name',
                                     'field_switch_contact_person',
                                     'field_switch_notification',
                                     'field_switch_newsletter',
                                 ] as $property) {
                            $data[$property] = !empty($formData[$property]) ? $formData[$property] : 0;
                        }
                        $data['state_field_label'] = !empty($formData['state_field_label'])
                            ? $formData['state_field_label']
                            : null;
                        $data['contribution_confirmation_info'] = $formData['contribution_confirmation_info'];
                        $data['anonymous_contribution'] = (int) false;
                        $consultationModel->update($data, ['kid=?' => $consultationId]);
                        $db->commit();
                    } catch (Exception $e) {
                        $db->rollback();
                        throw $e;
                    }

                    $this->_flashMessenger->addMessage('Form settings were updated.', 'success');
                    $this->redirect($this->view->url(['action' => 'contribution-submission-form']), ['prependBase' => false]);
                } else {
                    $this->_flashMessenger->addMessage(
                        'Form settings cannot be updated. Please check the errors marked in the form below and try again.',
                        'error'
                    );

                    $anonymousForm->populate($consultationModel->find($consultationId)->current()->toArray());
                }
            } elseif (isset($formData['submitAnonymous'])) {
                if ($anonymousForm->isValid($formData)) {
                    $db = $consultationModel->getAdapter();
                    $db->beginTransaction();
                    try {
                        $consultationModel->update(
                            [
                                'anonymous_contribution' => (int) true,
                                'anonymous_contribution_finish_info' => $formData['anonymous_contribution_finish_info'],
                            ],
                            ['kid=?' => $consultationId]
                        );
                        $db->commit();
                    } catch (Exception $e) {
                        $db->rollback();
                        throw $e;
                    }

                    $this->_flashMessenger->addMessage('Form settings were updated.', 'success');
                    $this->redirect($this->view->url(['action' => 'contribution-submission-form']), ['prependBase' => false]);
                } else {
                    $this->_flashMessenger->addMessage(
                        'Form settings cannot be updated. Please check the errors marked in the form below and try again.',
                        'error'
                    );

                    $registrationForm->populate($consultationModel->find($consultationId)->current()->toArray());
                }
            } else {
                $this->_flashMessenger->addMessage(
                    'Form settings cannot be updated. Please check the errors marked in the form below and try again.',
                    'error'
                );

                $consultationData = $consultationModel->find($consultationId)->current()->toArray();
                $registrationForm->populate($consultationData);
                $anonymousForm->populate($consultationData);
            }
        } else {
            $consultationData = $consultationModel->find($consultationId)->current()->toArray();
            $registrationForm->populate($consultationData);
            $anonymousForm->populate($consultationData);
        }

        $this->view->anonymousSwitch = $anonymousSwitch;
        $this->view->registrationForm = $registrationForm;
        $this->view->anonymousForm = $anonymousForm;
    }

    public function groupsAction()
    {
        $consultationId = $this->_request->getParam('kid', 0);
        if (empty($consultationId)) {
            $this->_flashMessenger->addMessage('No consultation provided.', 'error');
            $this->redirect('/admin');
        }

        $formGroups = new Admin_Form_ConsultationGroups($this->_consultation);
        $groupContributorAge = new Model_ContributorAge();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($formGroups->isValid($formData)) {
                $groupService = new Service_Groups();

                $db = $groupContributorAge->getAdapter();
                $db->beginTransaction();
                $error = false;
                try {
                    $groupService->updateGroupAges(
                        isset($formData['contributorAges']) ? $formData['contributorAges'] : [],
                        $formData['activateToInfinityAge'] && isset($formData['toInfinityAgeFrom'])
                            ? $formData['toInfinityAgeFrom']
                            : null,
                        $this->_consultation
                    );
                    $groupService->updateGroupSizes(
                        isset($formData['groupSizes']) ? $formData['groupSizes'] : [],
                        $formData['activateToInfinitySize'] && isset($formData['toInfinitySizeFrom'])
                            ? $formData['toInfinitySizeFrom']
                            : null,
                        $this->_consultation
                    );
                    (new Model_Consultations())->update(
                        ['groups_no_information' => (int) $formData['activateNoInformationValue']],
                        ['kid = ?' => $this->_consultation['kid']]
                    );
                    $db->commit();
                } catch (Service_Exception_GroupsDeletingException $e) {
                    $db->rollback();
                    $this->_flashMessenger->addMessage('Cannot delete already used groups.', 'error');
                    $restoredInterval = $e->getInterval();
                    $formData[$e->getIntervalGroup()][$restoredInterval['id']] = $restoredInterval;
                    $error = true;
                } catch (Service_Exception_GroupsEditingException $e) {
                    $db->rollback();
                    $this->_flashMessenger->addMessage(
                        'Cannot edit groups after consultation has been started.',
                        'error'
                    );
                    if ($e->getInterval() !== null) {
                        foreach ($e->getInterval() as $id => $interval) {
                            $formData[$e->getIntervalGroup()][$id] = [
                                'from' => $interval['from'],
                                'to' => $interval['to'],
                            ];
                        }
                    }
                    if ($e->getToInfinityOriginal() !== null) {
                        foreach ($e->getToInfinityOriginal() as $name => $value)
                        $formData[$name] = $value;
                    }
                    $error = true;
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }

                if ($error) {
                    $formData['singleFrom'] = 1;
                    $formData['singleTo'] = 2;
                    $formGroups->populate($formData);
                } else {
                    $this->_flashMessenger->addMessage('Group cluster settings were updated.', 'success');
                    $this->redirect($this->view->url(['action' => 'groups']), ['prependBase' => false]);
                }
            } else {
                $this->_flashMessenger->addMessage(
                    'Age groups settings cannot be updated. Please check the errors marked in the form below and try again.',
                    'error'
                );
            }
        } else {
            $data = ['activateNoInformationValue' => $this->_consultation['groups_no_information']];

            $sizes = (new Model_GroupSize())->getByConsultation($this->_consultation['kid']);
            $ages = (new Model_ContributorAge())->getByConsultation($this->_consultation['kid']);

            foreach ($sizes as $size) {
                if ($size['from'] > 0 && $size['to'] === null) {
                    $data['activateToInfinitySize'] = true;
                    $data['toInfinitySizeFrom'] = $size['from'];
                    continue;
                }
                $data['groupSizes'][$size['id']]['from'] = $size['from'];
                $data['groupSizes'][$size['id']]['to'] = $size['to'];
            }

            foreach ($ages as $age) {
                if ($age['from'] > 0 && $age['to'] === null) {
                    $data['activateToInfinityAge'] = true;
                    $data['toInfinityAgeFrom'] = $age['from'];
                    continue;
                }
                $data['contributorAges'][$age['id']]['from'] = $age['from'];
                $data['contributorAges'][$age['id']]['to'] = $age['to'];
            }
            $formGroups->populate($data);
        }

        $this->view->formGroups = $formGroups;
        $this->view->jsTranslations = $this->getJsTranslations();
    }

    /**
     * @return array
     */
    private function getJsTranslations()
    {
        return [
            'contribution_interval_confirm_delete' => $this->view->translate(
                'Dou you want to delete this interval?'
            ),
        ];
    }
}
