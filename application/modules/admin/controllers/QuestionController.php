<?php

class Admin_QuestionController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_adminIndexURL = null;


    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->initView();
        $this->_adminIndexURL = $this->view->url(['controller' => 'index', 'action' => 'index']);
    }

    /**
     * Show the list of questions
     */
    public function indexAction()
    {
        $kid = $this->getRequest()->getParam('kid', null);
        $questionModel = new Model_Questions();
        $questions = $questionModel->fetchAll(
            $questionModel
                ->select()
                ->from($questionModel->info(Model_Questions::NAME), ['qi', 'nr', 'q'])
                ->where('kid = ?', $kid)
                ->order('nr ASC')
        );

        $this->view->questions = $questions;
        $this->view->kid = $kid;
        $this->view->form = new Admin_Form_ListControl();
    }

    public function createAction()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $consultation = null;
        $form = null;
        if ($kid > 0) {
            $consultationModel = new Model_Consultations();
            $consultation = $consultationModel->getById($kid);
            if (!empty($consultation)) {
                $form = new Admin_Form_Question();
                $form->setAction($this->view->baseUrl() . '/admin/question/create/kid/' . $kid);
                if ($this->getRequest()->isPost()) {
                    if ($form->isValid($this->getRequest()->getPost())) {
                        $questionModel = new Model_Questions();
                        // get max qi:
                        $maxId = $questionModel->getMaxId();
                        // create new qi:
                        $newQi = intval($maxId)+rand(1,300);
                        $questionRow = $questionModel->createRow($form->getValues());
                        $questionRow->qi = $newQi;
                        $questionRow->kid = $kid;
                        $questionRow->time_modified = Zend_Date::now()->get('YYYY-MM-dd HH:mm:ss');
                        $questionRow->ln = 'de';
                        $newId = $questionRow->save();
                        if ($newId > 0) {
                            $this->_flashMessenger->addMessage('Neue Frage wurde erstellt.', 'success');
                        } else {
                            $this->_flashMessenger->addMessage('Erstellen neuer Frage fehlgeschlagen!', 'error');
                        }

                        $this->_redirect($this->view->url(array(
                            'action' => 'index',
                            'kid' => $kid
                        )), array('prependBase' => false));
                    } else {
                        $form->populate($form->getValues());
                    }
                }
            }
        }
        $this->view->assign(array(
            'consultation' => $consultation,
            'form' => $form
        ));
    }

    public function editAction()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $consultation = null;
        $form = null;
        if ($kid > 0) {
            $consultationModel = new Model_Consultations();
            $consultation = $consultationModel->getById($kid);
            if (!empty($consultation)) {
                $qid = $this->getRequest()->getParam('qid', 0);
                if ($qid > 0) {
                    $questionModel = new Model_Questions();
                    $questionRow = $questionModel->find($qid)->current();
                    $form = new Admin_Form_Question();
                    if ($this->getRequest()->isPost()) {
                        // Formular wurde abgeschickt und muss verarbeitet werden
                        $params = $this->getRequest()->getPost();
                        if ($form->isValid($params)) {
                            $questionRow->setFromArray($form->getValues());
                            $questionRow->time_modified = Zend_Date::now()->get('YYYY-MM-dd HH:mm:ss');
                            $questionRow->save();
                            $this->_flashMessenger->addMessage('Änderungen wurden gespeichert.', 'success');
                            $question = $questionRow->toArray();
                        } else {
                            $this->_flashMessenger->addMessage('Bitte überprüfe die Eingaben und versuche es noch einmal!', 'error');
                            $question = $params;
                        }
                    } else {
                        $question = $questionModel->getById($qid);
                    }
                    $form->populate($question);
                }
            }
        }

        $this->view->assign(array(
            'consultation' => $consultation,
            'form' => $form
        ));
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
                $this->_flashMessenger->addMessage('Question deleted.', 'success');
            } else {
                $this->_flashMessenger->addMessage('Question could not be deleted as there are inputs attached to it.', 'error');
            }
        }

        $this->_redirect($this->view->url(['action' => 'index']));
    }
}
