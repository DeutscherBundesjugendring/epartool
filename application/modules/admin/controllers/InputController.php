<?php

class Admin_InputController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_consultation = null;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_consultation = $this->_helper->consultationGetter($this->_request->getParams());
        $this->view->consultation = $this->_consultation;
    }

    /**
     * Shows the list of questions and contributers
     */
    public function indexAction()
    {
        $userModel = new Model_Users();
        $questionsModel = new Model_Questions();
        $inputModel = new Model_Inputs();

        $users = $userModel->fetchAll(
            $userModel
                ->select()
                ->setIntegrityCheck(false)
                ->from(['u' => $userModel->info(Model_Users::NAME)], ['uid', 'name', 'email', 'cmnt'])
                ->join(
                    ['i' => $inputModel->info(Model_Inputs::NAME)],
                    'i.uid = u.uid',
                    ['inputCount' => 'COUNT(*)']
                )
                ->join(
                    ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                    'q.qi = i.qi',
                    null
                )
                ->group('uid')
                ->where('q.kid = ?', $this->_consultation['kid'])
        );

        $questions = $questionsModel->fetchAll(
            $questionsModel
                ->select()
                ->setIntegrityCheck(false)
                ->from($questionsModel->info(Model_Questions::NAME), ['qi', 'q', 'nr'])
                ->joinLeft(
                    ['tmp1' => new Zend_Db_Expr(
                        '(SELECT qi AS tmpQi, COUNT(*) AS inputCountTotal FROM inpt GROUP BY qi)'
                    )],
                    $questionsModel->info(Model_Questions::NAME) . '.qi = ' . 'tmp1.tmpQi'
                )
                ->joinLeft(
                    ['tmp2' => new Zend_Db_Expr(
                        "(SELECT qi AS tmpQi, COUNT(*) AS inputCountUnread FROM inpt WHERE `is_confirmed` IS NULL"
                        . " GROUP BY qi)"
                    )],
                    $questionsModel->info(Model_Questions::NAME) . '.qi = ' . 'tmp2.tmpQi'
                )
                ->where('kid = ?', $this->_consultation['kid'])
        );

        $this->view->questions = $questions;
        $this->view->users = $users;
    }

    /**
     * List inputs for the given question
     */
    public function listByQuestionAction()
    {
        $qid = $this->_request->getParam('qi', null);
        $isUnread = $this->_request->getParam('isUnread', null);

        $questionsModel = new Model_Questions();
        $question = $questionsModel->fetchRow(
            $questionsModel
                ->select()
                ->from($questionsModel->info(Model_Questions::NAME), ['q', 'nr', 'qi'])
                ->where('qi = ?', $qid)
        );

        $wheres = [
            $questionsModel->info(Model_Questions::NAME) . '.qi = ?' => $qid,
            $questionsModel->info(Model_Questions::NAME) . '.kid = ?' => $this->_consultation['kid'],
        ];
        if ($isUnread) {
            $wheres[] = (new Model_Inputs())->info(Model_Inputs::NAME) . '.is_confirmed IS NULL';
        }

        $inputModel = new Model_Inputs();

        $form = new Admin_Form_InputSort();
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {
                $sortColumn = $form->getValue('sortColumn');
                $this->view->inputs = $inputModel->getComplete($wheres, $sortColumn);
            } else {
                $this->view->inputs = $inputModel->getComplete($wheres);
            }
        } else {
            $this->view->inputs = $inputModel->getComplete($wheres);
        }

        $adminActionCsrfSess = new Zend_Session_Namespace('adminActionCsrf');
        $adminActionCsrfSess->token = md5(mt_rand(1, 100000) . time());

        $this->view->sortForm = $form;
        $this->view->question = $question;
        $this->view->form = new Admin_Form_ListControl();
        $this->view->tags = (new Model_Tags())->getAll()->toArray();
        $this->view->inputsWithDiscussion = $inputModel->getInputsWithDiscussionIds(['qi=?' => $qid]);
        $this->view->csrfToken = $adminActionCsrfSess->token;
    }

    public function changeStatusAction()
    {
        $token = $this->_request->getParam('token', null);
        $tid = $this->_request->getParam('tid', null);

        $properties = ['voting' => 'is_votable', 'blocking' => 'is_confirmed'];
        $property = $this->_request->getParam('property', null);

        if ((new Zend_Session_Namespace('adminActionCsrf'))->token !== $token
            || !$tid || !isset($properties[$property])) {
            $response = $this->getResponse();
            $response->setHttpResponseCode(403);
            $response->sendResponse();
        }

        $nextStatus = [
            'blocking' => ['1' => null, '0'=> '1', null => '0'],
            'voting' => ['1' => '0', '0' => null, null => '1'],
        ];
        $button = [
            'blocking' => [
                '0' => ['label' => 'Blocked', 'iconClass' => 'remove', 'labelClass' => 'danger'],
                '1' => ['label' => 'Confirmed', 'iconClass' => 'ok', 'labelClass' => 'success'],
                null => ['label' => 'Unknown', 'iconClass' => 'question-sign', 'labelClass' => 'default'],
                ],
            'voting' => [
                '1' => ['label' => 'Yes', 'iconClass' => 'ok', 'labelClass' => 'success'],
                '0' => ['label' => 'No', 'iconClass' => 'remove', 'labelClass' => 'danger'],
                null => ['label' => 'Unknown', 'iconClass' => 'question-sign', 'labelClass' => 'default'],
                ],
        ];
        $contributionModel = new Model_Inputs();
        $contribution = $contributionModel->find($tid)->current();
        $contributionModel->update(
            [$properties[$property] => $nextStatus[$property][$contribution[$properties[$property]]]],
            ['tid = ?' => $tid]
        );

        $adminActionCsrfSess = new Zend_Session_Namespace('adminActionCsrf');
        $adminActionCsrfSess->token = md5(mt_rand(1, 100000) . time());
        $this->_helper->json([
            'iconClass' => $button[$property][$nextStatus[$property][$contribution[$properties[$property]]]]['iconClass'],
            'labelClass' => $button[$property][$nextStatus[$property][$contribution[$properties[$property]]]]['labelClass'],
            'label' => Zend_Registry::get('Zend_Translate')->translate(
                $button[$property][$nextStatus[$property][$contribution[$properties[$property]]]]['label']
            ),
            'status' => $nextStatus[$property][$contribution[$properties[$property]]],
            'token' => $adminActionCsrfSess->token,
        ]);
    }

    /**
     * List inputs for the given user
     */
    public function listByUserAction()
    {
        $uid = $this->_request->getParam('uid', null);
        $inputModel = new Model_Inputs();

        $this->view->user = (new Model_Users())->getById($uid);
        $this->view->user_info = (new Model_User_Info())->getLatestByUserAndConsultation(
            $uid,
            $this->_consultation['kid']
        );
        $this->view->inputs = $inputModel->getCompleteGroupedByQuestion(
            [
                (new Model_Users())->info(Model_Users::NAME) . '.uid = ?' => $uid,
                (new Model_Questions())->info(Model_Questions::NAME) . '.kid = ?' => $this->_consultation['kid'],
            ]
        );

        $adminActionCsrfSess = new Zend_Session_Namespace('adminActionCsrf');
        $adminActionCsrfSess->token = md5(mt_rand(1, 100000) . time());

        $this->view->userGroupSizes = (new Model_GroupSize())->getOptionsByConsultation($this->_consultation['kid']);
        $this->view->contributorAges = (new Model_ContributorAge())
            ->getOptionsByConsultation($this->_consultation['kid']);
        $this->view->form = new Admin_Form_ListControl();
        $this->view->inputsWithDiscussion = $inputModel->getInputsWithDiscussionIds(['uid=?' => $uid]);
        $this->view->csrfToken = $adminActionCsrfSess->token;
    }

    /**
     * Edit Input
     */
    public function editAction()
    {
        $tid = $this->_request->getParam('tid', 0);
        $uid = $this->_request->getParam('uid', 0);
        $qi = $this->_request->getParam('qi', 0);

        $session = new Zend_Session_Namespace('inputEdit');

        if (!$this->getRequest()->isPost()) {
            $session->urlQi = $this->getRequest()->getParam('qi', 0);
        }

        if ($this->getRequest()->getParam('return', null) === 'votingprepare') {
            $url = $this->view->url(
                [
                    'controller' => 'votingprepare',
                    'action' => 'overview',
                    'return' => null,
                    'tid' => null,
                ]
            );
            $cancelUrl = $this->view->returnUrl = $url;
        } elseif ($session->urlQi > 0) {
            $url = $this->view->url(['action' => 'list-by-question', 'qi' => $qi, 'tid' => null]);
            $cancelUrl = $this->view->returnUrl = $url;
        } else {
            $url = $this->view->url(['action' => 'list-by-user', 'uid' => $uid, 'tid' => null]);
            $cancelUrl = $this->view->returnUrl = $url;
        }

        $inputModel = new Model_Inputs();
        $form = new Admin_Form_Input($cancelUrl);

        $inputRow = $inputModel->getById($tid);

        if (!$inputRow) {
            $this->_flashMessenger->addMessage('Contribution not found.', 'error');
            $this->redirect($cancelUrl);
        }

        if (!$qi) {
            $qi = $inputRow['qi'];
        }
        $projectSettings = (new Model_Projects())->find(Zend_Registry::get('systemconfig')->project)->current();
        $question = (new Model_Questions())->find($qi)->current();
        $form->setVideoEnabled(
            $question['video_enabled']
            && ($projectSettings['video_facebook_enabled']
                || $projectSettings['video_youtube_enabled']
                || $projectSettings['video_vimeo_enabled']
            )
        );
        
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {
                $formValues = $form->getValues();
                if (isset($data['delete'])) {

                    try {
                        $deleteStatus = $inputModel->deleteById($tid);
                    } catch (Zend_Db_Statement_Exception $e) {
                        if ($e->getCode() === 23000) { // Integrity constraint violation
                            $this->_flashMessenger->addMessage(
                                "This contribution can't be deleted, because it has already been voted upon.",
                                'error'
                            );
                            $this->redirect($this->view->url(), ['prependBase' => false]);
                        }
                        throw $e;
                    }

                    if ($deleteStatus > 0) {
                        $this->_flashMessenger->addMessage('Contribution was deleted.', 'success');
                        $this->redirect($url, ['prependBase' => false]);
                    }
                    $this->_flashMessenger->addMessage('Delete contribution failed.', 'error');
                } elseif(isset($data['submit'])) {
                    if (!$formValues['tags']) {
                        $formValues['tags'] = [];
                    }
                    $updated = $inputModel->updateById($tid, $formValues);
                    if ($updated == $tid) {
                        $this->_flashMessenger->addMessage('Changes saved.', 'success');
                        unset($session->urlQi);
                        $this->redirect($url, ['prependBase' => false]);
                    } else {
                        $this->_flashMessenger->addMessage('Contribution update failed.', 'error');
                    }
                }
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                $form->populate($data);
            }
        } else {
            $data = $inputRow;
            if ($data['video_service'] !== null) {
                $project = (new Model_Projects)->find((new Zend_Registry())->get('systemconfig')->project)->current();
                if (!$project['video_' . $data['video_service'] . '_enabled']) {
                    $data['video_service'] = null;
                    $data['video_id'] = null;
                    $this->_flashMessenger->addMessage(
                        'Video service used for embedding video in this contribution was disabled. Video will be deleted after save contribution.',
                        'error'
                    );
                }
            }
            $form->populate($data);
            if (!empty($inputRow['tags'])) {
                $tagsSet = array();
                foreach ($inputRow['tags'] as $tag) {
                    $tagsSet[] = $tag['tg_nr'];
                }
                $form->setDefault('tags', $tagsSet);
                if ($inputRow['is_confirmed'] === null) {
                    $form->getElement('is_confirmed')->setValue(1);
                }
            }
        }
        
        $this->view->form = $form;
        $this->view->tid = $tid;
        $this->view->authorId = $inputRow['uid'];
    }

    public function createAction()
    {
        $consultationId = $this->_request->getParam('kid', 0);

        $session = new Zend_Session_Namespace('inputCreate');

        if ($session->urlQid > 0) {
            $cancelUrl = $this->view->returnUrl = $this->view->url([
                'action' => 'index',
                'kid' => $consultationId,
            ]);
        } else {
            $cancelUrl = $this->view->returnUrl = $this->view->url(['action' => 'index','kid' => $consultationId]);
        }

        $inputModel = new Model_Inputs();
        $form = new Admin_Form_CreateInput($cancelUrl);
        $form->setVideoEnabled(false);

        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {
                $formValues = $form->getValues();
                $formValues['tags'] = $formValues['tags'] ? $formValues['tags'] : [];
                $inputModel->getAdapter()->beginTransaction();
                try {
                    $inputModel->createContribution($formValues);
                    $this->_flashMessenger->addMessage('Contribution was created.', 'success');
                    $inputModel->getAdapter()->commit();
                    $this->redirect($this->view->url([
                        'action' => 'index',
                        'kid' => $consultationId,
                    ]), ['prependBase' => false]);
                } catch (\Exception $e) {
                    $inputModel->getAdapter()->rollBack();
                    throw $e;
                }
            } else {
                $this->_flashMessenger->addMessage(
                    'New contribution cannot be created. Please check the errors marked in the form below and try again.',
                    'error'
                );
                $form->populate($data);
            }
        } else {
            $this->_flashMessenger->addMessage(
                    'Video contribution settings are inherited from Question, therefore it is possible to add a video only after saving this Contribution thus linking it to a Question.',
                    'info'
                );
            $form->populate(['is_confirmed_by_user' => null, 'is_confirmed' => true, 'is_votable' => null]);
        }

        $this->view->form = $form;
    }

    /**
     * Makes changes to Inputs from the input list context in bulk and individually
     */
    public function editListAction()
    {
        $form = new Admin_Form_ListControl();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $inputModel = new Model_Inputs();
            $data = $this->getRequest()->getPost();
            $returnUrl = $data['return_url'];

            if (!empty($data['bulkAction']) && !empty($data['inp_list'])) {
                switch ($data['bulkAction']) {
                    case 'delete':
                        try {
                            $nr = $inputModel->deleteBulk($data['inp_list']);
                        } catch (Zend_Db_Statement_Exception $e) {
                            if ($e->getCode() === 23000) { // Integrity constraint violation
                                $this->_flashMessenger->addMessage(
                                    "This contribution can't be deleted, because it has already been voted upon.",
                                    'error'
                                );
                                $this->redirect($returnUrl);
                            }
                            throw $e;
                        }
                        $msg = sprintf($this->view->translate('%d contributions have been deleted.'), $nr);
                        $this->_flashMessenger->addMessage($msg, 'success');
                        break;
                    case 'block':
                        $nr = $inputModel->editBulk($data['inp_list'], array('is_confirmed' => false));
                        $msg = sprintf($this->view->translate('%d contributions have been blocked.'), $nr);
                        $this->_flashMessenger->addMessage($msg, 'success');
                        break;
                    case 'publish':
                        $nr = $inputModel->editBulk($data['inp_list'], array('is_confirmed' => true));
                        $msg = sprintf($this->view->translate('%d contributions have been unblocked.'), $nr);
                        $this->_flashMessenger->addMessage($msg, 'success');
                        break;
                }
            } elseif (!empty($data['delete'])) {
                try {
                    $inputModel->deleteById($data['delete']);
                } catch (Zend_Db_Statement_Exception $e) {
                    if ($e->getCode() === 23000) { // Integrity constraint violation
                        $this->_flashMessenger->addMessage(
                            "This contribution can't be deleted, because it has already been voted upon.",
                            'error'
                        );
                        $this->redirect($returnUrl);
                    }
                    throw $e;
                }
                $this->_flashMessenger->addMessage('Contribution has been deleted.', 'success');
            }
        }

        $this->redirect(!empty($returnUrl) ? $returnUrl : $this->view->baseUrl() . '/admin', ['prependBase' => false]);
    }

    /**
     * Export inputs as CSV file
     */
    public function exportAction()
    {
        $qid = $this->_request->getParam('qi', 0);
        $kid = $this->_request->getParam('kid', 0);
        $cod = $this->_request->getParam('cod', 'utf8');
        $mod = $this->_request->getParam('mod', 'cnf');
        $tag = $this->_request->getParam('tg');

        if ($kid == 0) {
            $this->_flashMessenger->addMessage('No consultation provided.', 'error');
            $this->redirect('/admin');
        }
        if ($qid == 0) {
            $this->_flashMessenger->addMessage('No question provided.', 'error');
            $this->redirect('/admin');
        }

        $questionModel = new Model_Questions();
        $question = $questionModel->find($qid)->current()->toArray();

        $inputModel = new Model_Inputs();
        $csv = $inputModel->getCSV($kid, $qid, $mod, $tag);

        if ($cod == 'xls') {
            $charset =    mb_detect_encoding($csv, "UTF-8, ISO-8859-1, ISO-8859-15", true);
            if ($charset) {
                $csv =    mb_convert_encoding($csv, "Windows-1252", $charset);
                $cod = "windows-1252";
            }
        } else {
            $cod = "utf-8";
        }

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        header("Content-type: text/csv");
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Content-Disposition: attachment; filename=inputs_'
            . (isset($question['nr']) ? $question['nr'] . '_' : '') . 'qid' . $qid . '_' . $mod . '_'
            . gmdate('Y-m-d_H\hi\m') . '_' . $cod . '.csv');
        header('Pragma: no-cache');

        // @codingStandardsIgnoreLine
        echo $csv;
        exit;
    }
}
