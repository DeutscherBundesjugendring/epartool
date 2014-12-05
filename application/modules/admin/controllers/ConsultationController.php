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
        $inputs = $inputsModel->getComplete(
            [
                $inputsModel->info(Model_Inputs::NAME) . '.block = ?' => 'u',
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
            $mediaService = new Service_Media();

            if ($form->isValid($postData)) {
                $consultationRow = $consultationModel->createRow($postData);
                $consultationRow->proj = implode(',', $form->getElement('proj')->getValue());
                $filename = $form->getElement('img_file')->getFileName();
                if ($filename) {
                    $consultationRow->img_file = $mediaService->sanitizeFilename($filename);
                }

                $newKid = $consultationRow->save();

                if ($newKid) {
                    $mediaService->createDir($newKid);
                    if ($filename) {
                        $mediaService->upload(
                            Dbjr_File::pathinfoUtf8($filename, PATHINFO_BASENAME),
                            $newKid
                        );
                    }

                    $this->_flashMessenger->addMessage('New consultation has been created.', 'success');
                    $this->_redirect('/admin/consultation/edit/kid/' . $consultationRow->kid);
                } else {
                    $this->_flashMessenger->addMessage('Creating new consultation failed.', 'error');
                }
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                $form->populate($this->getRequest()->getPost());
            }
        } else {
            $form->getElement('ord')->setValue($consultationModel->getMaxOrder() + 1);
        }

        foreach ($form->getElements() as $element) {
            $element->clearFilters();
            if ($element->getName() != 'proj') {
                $element->setValue(html_entity_decode($element->getValue(), ENT_COMPAT, 'UTF-8'));
            }
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
        $form->getElement('img_file')->setIsLockDir(true);

        if ($this->getRequest()->isPost() && false !== $this->getRequest()->getPost('submit', false)) {
            $posts = $this->getRequest()->getPost();
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_consultation->setFromArray($form->getValues());
                $this->_consultation->proj = implode(',', $form->getElement('proj')->getValue());
                $this->_consultation->discussion_from = $this->_consultation->discussion_from ? $this->_consultation->discussion_from : null;
                $this->_consultation->discussion_to = $this->_consultation->discussion_to ? $this->_consultation->discussion_to : null;
                $this->_consultation->save();
                $this->_flashMessenger->addMessage('Consultation saved.', 'success');

                $this->_redirect('/admin/consultation/edit/kid/' . $this->_consultation->kid);
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                $form->populate($form->getValues());
            }
        } else {
            // Martin 2014-11-28
            // This should not be here as the default values in db should be null.
            // However that is not the case and changing it could break all the display logic on the front end.
            if ($this->_consultation->inp_fr === '0000-00-00 00:00:00') {
                $this->_consultation->inp_fr = null;
            }
            if ($this->_consultation->inp_to === '0000-00-00 00:00:00') {
                $this->_consultation->inp_to = null;
            }
            if ($this->_consultation->vot_fr === '0000-00-00 00:00:00') {
                $this->_consultation->vot_fr = null;
            }
            if ($this->_consultation->vot_to === '0000-00-00 00:00:00') {
                $this->_consultation->vot_to = null;
            }
            if ($this->_consultation->spprt_fr === '0000-00-00 00:00:00') {
                $this->_consultation->spprt_fr = null;
            }
            if ($this->_consultation->spprt_to === '0000-00-00 00:00:00') {
                $this->_consultation->spprt_to = null;
            }

            $form->populate($this->_consultation->toArray());
            $form->getElement('proj')->setValue(explode(',', $this->_consultation['proj']));
        }

        foreach ($form->getElements() as $element) {
            $element->clearFilters();
            if ($element->getName() != 'proj') {
                $element->setValue(html_entity_decode($element->getValue(), ENT_COMPAT, 'UTF-8'));
            }
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
                ->getCountByQuestionFiltered($question['qi'], array(
                    array(
                        'field' => 'user_conf',
                        'operator' => '=',
                        'value' => 'c'
                    )
                ));
            $questions[$question['qi']]['nrInputsVoting'] = $inputsModel
                ->getCountByQuestionFiltered($question['qi'], array(
                    array(
                        'field' => 'vot',
                        'operator' => '=',
                        'value' => 'y'
                    )
                ));
        }

        $votesIndivModel = new Model_Votes_Individual();
        $votesRightsModel = new Model_Votes_Rights();

        $this->view->assign(array(
            'nrParticipants' => $inputsModel->getCountParticipantsByConsultation($kid),
            'nrInputs' => $inputsModel->getCountByConsultation($kid, false),
            'nrInputsConfirmed' => $inputsModel->getCountByConsultationFiltered($kid,
                array(array('field' => 'user_conf', 'operator' => '=', 'value' => 'c'))),
            'nrInputsUnconfirmed' => $inputsModel->getCountByConsultationFiltered($kid,
                array(array('field' => 'user_conf', 'operator' => '=', 'value' => 'u'))),
            'nrInputsBlocked' => $inputsModel->getCountByConsultationFiltered($kid,
                array(array('field' => 'block', 'operator' => '=', 'value' => 'y'))),
            'nrInputsVoting' => $inputsModel->getCountByConsultationFiltered($kid,
                array(array('field' => 'vot', 'operator' => '=', 'value' => 'y'))),
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
        if ($current_user->lvl !== 'adm') {
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
                $this->redirect($this->view->url());
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
}
