<?php

class Admin_QuestionController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_adminIndexURL = null;
    protected $_consultation;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->initView();
        $this->_adminIndexURL = $this->view->url(['controller' => 'index', 'action' => 'index']);
        $this->_consultation = $this->_helper->consultationGetter($this->_request->getParams());
    }

    /**
     * Show the list of questions
     */
    public function indexAction()
    {
        $questionModel = new Model_Questions();
        $questions = $questionModel->fetchAll(
            $questionModel
                ->select()
                ->from($questionModel->info(Model_Questions::NAME), ['qi', 'nr', 'q'])
                ->where('kid = ?', $this->_consultation['kid'])
                ->order(['nr ASC', 'q ASC'])
        );

        $this->view->questions = $questions;
        $this->view->consultation = $this->_consultation;
        $this->view->form = new Admin_Form_ListControl();
    }

    public function createAction()
    {
        $form = new Admin_Form_Question($this->_consultation['kid']);
        $form->setAction($this->view->baseUrl() . '/admin/question/create/kid/' . $this->_consultation['kid']);
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $questionRow = (new Model_Questions())->createRow($form->getValues());
                $questionRow->kid = $this->_consultation['kid'];
                $questionRow->time_modified = Zend_Date::now()->get('YYYY-MM-dd HH:mm:ss');
                $questionRow->ln = 'de';
                if ($questionRow['nr'] === '') {
                    $questionRow['nr'] = null;
                }
                $questionRow->save();
                $this->_flashMessenger->addMessage('New question has been created.', 'success');

                $this->redirect(
                    $this->view->url(['action' => 'index', 'kid' => $this->_consultation['kid']]),
                    ['prependBase' => false]
                );
            }
            $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
        }

        $this->view->consultation = $this->_consultation;
        $this->view->form = $form;
    }

    public function editAction()
    {
        $qid = $this->getRequest()->getParam('qid', 0);
        if ($qid > 0) {
            $questionModel = new Model_Questions();
            $form = new Admin_Form_Question($this->_consultation['kid']);
            if ($this->getRequest()->isPost()) {
                $params = $this->getRequest()->getPost();
                if ($form->isValid($params)) {
                    $questionRow = $questionModel->find($qid)->current();
                    $questionRow->setFromArray($form->getValues());
                    $questionRow->time_modified = Zend_Date::now()->get('YYYY-MM-dd HH:mm:ss');
                    if ($questionRow['nr'] === '') {
                        $questionRow['nr'] = null;
                    }
                    $questionRow->save();
                    $this->_flashMessenger->addMessage('Changes saved.', 'success');
                    $question = $questionRow->toArray();
                } else {
                    $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                    $question = $params;
                }
            } else {
                $question = $questionModel->getById($qid);
            }
            $form->populate($question);
        }

        $this->view->consultation = $this->_consultation;
        $this->view->form = $form;
    }

    /**
     * Deletes the question
     */
    public function deleteAction()
    {
        $form = new Admin_Form_ListControl();

        if ($form->isValid($this->getRequest()->getPost())) {
            $qid = $this->getRequest()->getPost('delete');
            $relatedInputs = (new Model_Inputs())->getByQuestion($qid);
            if (empty($relatedInputs)) {
                (new Model_Questions())->deleteById($qid);
                $this->_flashMessenger->addMessage('Question has been deleted.', 'success');
            } else {
                $this->_flashMessenger->addMessage(
                    'Question could not be deleted as there are contributions attached to it.',
                    'error'
                );
            }
        }

        $this->redirect($this->view->url(['action' => 'index']), ['prependBase' => false]);
    }
}
