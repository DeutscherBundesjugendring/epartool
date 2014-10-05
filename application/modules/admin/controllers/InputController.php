<?php

class Admin_InputController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;

    protected $_consultation = null;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $kid = $this->_request->getParam('kid', null);
        if ($kid) {
            $this->_consultation = (new Model_Consultations())->getById($kid);
            $this->view->consultation = $this->_consultation;
        }
    }

    /**
     * Shows the list of questions and contributers
     */
    public function indexAction()
    {
        $userModel = new Model_Users();
        $questionsModel = new Model_Questions();

        $inputTableAlias = 'inputTableAlias';
        $select = $userModel
            ->select()
            ->setIntegrityCheck(false)
            ->from($userModel->info(Model_Users::NAME), ['name', 'email', 'cmnt']);
        $userModel->selectInputCount($select, $inputTableAlias);
        $select
            ->join(
                ['q' => (new Model_Questions())->info(Model_Questions::NAME)],
                'q.qi = ' . $userModel->getAdapter()->quoteIdentifier($inputTableAlias) . '.qi'
            )
            ->where('q.kid = ?', $this->_consultation['kid']);
        $users = $userModel->fetchAll($select);

        $select = $questionsModel
            ->select()
            ->setIntegrityCheck(false)
            ->from($questionsModel->info(Model_Questions::NAME), ['qi', 'q'])
            ->where('kid = ?', $this->_consultation['kid']);
        $questionsModel->selectInputCountByQuestion($select, 'inputCountTotal');
        $questionsModel->selectUnreadInputCountByQuestion($select, 'inputCountUnread');
        $questions = $questionsModel->fetchAll($select);

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
            $wheres[(new Model_Inputs())->info(Model_Inputs::NAME) . '.block = ?'] = 'u';
        }

        $this->view->inputs = (new Model_Inputs())->getComplete($wheres);
        $this->view->question = $question;
        $this->view->form = new Admin_Form_ListControl();
    }

    /**
     * List inputs for the given user
     */
    public function listByUserAction()
    {
        $uid = $this->_request->getParam('uid', null);

        $this->view->user = (new Model_Users())->getById($uid);
        $this->view->user_info = (new Model_User_Info())->getLatestByUserAndConsultation($this->_consultation['kid'], $this->_consultation['kid']);
        $this->view->inputs = (new Model_Inputs())->getCompleteGroupedByQuestion(
            [
                (new Model_Users())->info(Model_Users::NAME) . '.uid = ?' => $uid,
                (new Model_Questions())->info(Model_Questions::NAME) . '.kid = ?' => $this->_consultation['kid'],
            ]
        );
        $this->view->userGroupSizes = Zend_Registry::get('systemconfig')->group_size_def->toArray();
        $this->view->form = new Admin_Form_ListControl();
    }

    /**
     * Edit Input
     */
    public function editAction()
    {
        $tid = $this->_request->getParam('tid', 0);
        $inputModel = new Model_Inputs();
        $form = new Admin_Form_Input();

        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {
                $origInput = $inputModel->find($tid)->current();
                if ($origInput->block !== 'n'
                    && $data['block'] === 'n'
                    && $data['user_conf'] !== 'r'
                ) {
                    (new Service_Notification_Input_Created())->notify(
                        [Service_Notification_Input_Created::PARAM_QUESTION_ID => $origInput['qi']]
                    );
                }
                $formValues = $form->getValues();
                if (!$formValues['tags']) {
                    $formValues['tags'] = [];
                }
                $updated = $inputModel->updateById($tid, $formValues);
                if ($updated == $tid) {
                    $this->_flashMessenger->addMessage('Eintrag aktualisiert', 'success');
                } else {
                    $this->_flashMessenger->addMessage('Aktualisierung fehlgeschlagen', 'error');
                }
            } else {
                $this->_flashMessenger->addMessage('Bitte Eingaben prüfen!', 'error');
                $form->populate($data);
            }
        } else {
            $inputRow = $inputModel->getById($tid);
            $form->populate($inputRow);
            if (!empty($inputRow['tags'])) {
                $tagsSet = array();
                foreach ($inputRow['tags'] as $tag) {
                    $tagsSet[] = $tag['tg_nr'];
                }
                $form->setDefault('tags', $tagsSet);
                if ($inputRow['block'] === 'u') {
                    $form->getElement('block')->setValue('n');
                }
            }
        }

        if ($this->getRequest()->getParam('return', null) === 'votingprepare') {
            $this->view->returnUrl = $this->view->url(
                [
                    'controller' => 'votingprepare',
                    'action' => 'overview',
                    'return' => null,
                    'tid' => null,
                ]
            );
        } elseif ($this->getRequest()->getParam('qi', null)) {
            $this->view->returnUrl = $this->view->url(['action' => 'list-by-question', 'tid' => null]);
        } else {
            $this->view->returnUrl = $this->view->url(['action' => 'list-by-user', 'tid' => null]);
        }
        $this->view->form = $form;
        $this->view->tid = $tid;
    }

    /**
     * Edit Inputs in bulk
     */
    public function editbulkAction()
    {
        $form = new Admin_Form_ListControl();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $inputModel = new Model_Inputs();
            $data = $this->_request->getPost();
            $returnUrl = $data['return_url'];

            switch ($data['action']) {
                case 'delete':
                    $nr = $inputModel->deleteBulk($data['inp_list']);
                    $msg = sprintf($this->view->translate('%d inputs were deleted.'), $nr);
                    $this->_flashMessenger->addMessage($msg, 'success');
                    break;
                case 'block':
                    $nr = $inputModel->editBulk($data['inp_list'], array('block' => 'y'));
                    $msg = sprintf($this->view->translate('%d inputs were blocked.'), $nr);
                    $this->_flashMessenger->addMessage($msg, 'success');
                    break;
                case 'publish':
                    $nr = $inputModel->editBulk($data['inp_list'], array('block' => 'n'));
                    $msg = sprintf($this->view->translate('%d inputs were unblocked.'), $nr);
                    $this->_flashMessenger->addMessage($msg, 'success');
                    break;
            }
        }

        $this->redirect(!empty($returnUrl) ? $returnUrl : $this->view->baseUrl() . '/admin');
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
            $this->_flashMessenger->addMessage('Keine Beteiligungsrunde angegeben.', 'error');
            $this->redirect('/admin');
        }
        if ($qid == 0) {
            $this->_flashMessenger->addMessage('Keine Frage angegeben.', 'error');
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
            . $question['nr'] . '_qid' . $qid . '_' . $mod . '_'
            . gmdate('Y-m-d_H\hi\m') . '_' . $cod . '.csv');
        header('Pragma: no-cache');

        echo $csv;
        exit;
    }
}
