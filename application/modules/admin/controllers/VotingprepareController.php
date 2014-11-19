<?php

class Admin_VotingprepareController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_consultation = null;

    /**
     * The current question id
     * @var integer
     */
    protected $_qid;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_params = $this->_request->getParams();
        $this->_consultation = $this->getKid($this->_params);
        $this->_qid = $this->getQid($this->_params);
        if (isset($this->_params["tid"])) {
            $this->_tid = $this->getTId($this->_params);
        }
    }

    /**
     * Shows the list of questions
     */
    public function indexAction()
    {
        $this->view->consultation = $this->_consultation;
    }

    /**
     * Returns parameters for list of inputs, questions and directories
     */
    public function overviewAction()
    {
        $form = new Admin_Form_ListControl();

        $wheres['inpt.qi = ?'] = $this->_qid;
        $fulltext = $this->getRequest()->getParam('fulltext', null);
        if ($fulltext) {
            $wheres['thes LIKE ?'] = '%' . $fulltext . '%';
        }

        $this->view->inputs = (new Model_Inputs())->fetchAllInputs($wheres);
        $this->view->fulltext = $fulltext;
        $this->view->consultation = $this->_consultation;
        $this->view->directories = (new Model_Directories())->getByQuestion($this->_qid);
        $this->view->tags = (new Model_Tags())->getAll()->toArray();
        $this->view->form = $form;
    }

    /**
     * Makes changes to Inputs from the input list contect in bulk and individualy
     */
    public function inputListControlAction()
    {
        if ($this->getRequest()->isPost()
            && (new Admin_Form_ListControl())->isValid($this->getRequest()->getPost())
        ) {
            $inputModel = new Model_Inputs();
            $inputIds = $this->getRequest()->getPost('inputIds');
            if ($inputIds) {
                if ($this->getRequest()->getPost('releaseBulk', null)) {
                    $count = $inputModel->editBulk($inputIds, ['block' => 'n']);
                    $msg = sprintf($this->view->translate('%d contributions have been released.'), $count);
                } elseif ($this->getRequest()->getPost('blockBulk', null)) {
                    $count = $inputModel->editBulk($inputIds, ['block' => 'y']);
                    $msg = sprintf($this->view->translate('%d contributions have been blocked.'), $count);
                } elseif ($this->getRequest()->getPost('enableVotingBulk', null)) {
                    $count = $inputModel->editBulk($inputIds, ['vot' => 'y']);
                    $msg = sprintf($this->view->translate('%d contributions have been sent to voting.'), $count);
                } elseif ($this->getRequest()->getPost('blockVotingBulk', null)) {
                    $count = $inputModel->editBulk($inputIds, ['vot' => 'n']);
                    $msg = sprintf($this->view->translate('%d contributions have been removed from voting.'), $count);
                } elseif ($this->getRequest()->getPost('deleteBulk', null)) {
                    $count = $inputModel->deleteBulk($inputIds);
                    $msg = sprintf($this->view->translate('%d contributions have been deleted.'), $count);
                } elseif ($this->getRequest()->getPost('sendToDictionaryBulk', null)) {
                    $count = $inputModel->update(
                        ['dir' => $this->getRequest()->getPost('sendToDictionaryId')],
                        ['tid IN (?)' => $inputIds]
                    );
                    $msg = sprintf($this->view->translate('%d contributions have been moved.'), $count);
                } elseif ($this->getRequest()->getPost('merge', null)) {
                    // This is awkward, but we need to utilise the same checkboxes as for bulk editing actions
                    // Essentially this only takes values from inputIds checkboxes and constructs an url to redirect to
                    return $this->redirect(
                        $this->view->url(['action' => 'merge', 'inputIds' => $this->getRequest()->getPost('inputIds', null)])
                    );
                }
                $this->_flashMessenger->addMessage($msg, 'success');
            } elseif ($this->getRequest()->getPost('delete', null)) {
                $inputModel->deleteById($this->getRequest()->getPost('delete', null));
                $this->_flashMessenger->addMessage('Contribution has been deleted.', 'success');
            }
        }

        $this->redirect($this->view->url(['action' => 'overview']));
    }

    /**
     * Inserts a new input from admin and set the old input as its child
     */
    public function splitAction()
    {
        $cancelUrl = $this->view->url(['action' => 'overview']);
        $inputModel = new Model_Inputs();
        $form = new Admin_Form_Input($cancelUrl);

        $inputId = $this->getRequest()->getParam('inputId');
        $this->addNewElements([$inputId], $form);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                $newTid = $inputModel->addInputs($postData);
                $this->_flashMessenger->addMessage('New contribution has been created.', 'success');
                $this->redirect($this->view->url());
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
            }
        }

        $this->view->inputs = $inputModel->fetchAllInputs(['tid = ?' => $inputId]);
        $this->view->consultation = $this->_consultation;
        $this->view->form = $form;
    }

    /**
     * Inserts a new input from admin and set the old inputs as childern
     */
    public function mergeAction()
    {
        $inputModel = new Model_Inputs();
        $inputIds = $this->getRequest()->getParam('inputIds');

        $cancelUrl = $this->view->url(['action' => 'overview', 'inputIds' => null]);
        $form = new Admin_Form_Input($cancelUrl);
        $this->addNewElements($inputIds, $form);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                $newTid = $inputModel->addInputs($postData);
                $this->_flashMessenger->addMessage('New contribution has been created.', 'success');
                $this->redirect($this->view->url(['action' => 'overview', 'inputIds' => null]));
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
            }
        }

        $this->view->inputs = $inputModel->fetchAllInputs(['tid IN (?)' => $inputIds]);
        $this->view->consultation = $this->_consultation;
        $this->view->form = $form;
    }

    /**
     * Creates a copy of an input
     */
    public function copyAction()
    {
        $inputModel = new Model_Inputs();
        $inputId = $this->getRequest()->getParam('inputId');

        $cancelUrl = $this->view->url(['action' => 'overview']);
        $form = new Admin_Form_Input($cancelUrl);
        $this->addNewElements([$inputId], $form);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                $newTid = $inputModel->addInputs($postData);
                $this->_flashMessenger->addMessage('Contribution has been copied. This is the copy.', 'success');
                $this->redirect(
                    $this->view->url(
                        ['controller' => 'input', 'action' => 'edit', 'tid' => $inputId, 'return' => 'votingprepare']
                    )
                );
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
            }
        } else {
            $origData = $inputModel->getById($inputId);
            $origData['uid'] = null;
            $origData['rel_id'] = $origData['tid'];
            unset($origData['when']);
            unset($origData['tid']);
            $form->populate($origData);
        }

        $this->view->consultation = $this->_consultation;
        $this->view->form = $form;
    }

    /**
     * Creates a new directory
     */
    public function createDirectoryAction()
    {
        $form = new Admin_Form_Directory();

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                $newId = (new Model_Directories())->insert(
                    [
                        'dir_name' => $postData['dir_name'],
                        'kid' => $this->_consultation['kid']
                    ]
                );
                $this->_flashMessenger->addMessage('New folder has been created.', 'success');
                $this->redirect($this->view->url());
            } else {
                $this->_flashMessenger->addMessage('Folder could not be created.', 'error');
            }
        }

        $this->view->form = $form;
        $this->view->consultation = $this->_consultation;
    }

    /**
     * Performs actions on the directory listings
     */
    public function directoryListControlAction()
    {
        $form = new Admin_Form_ListControl();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $directoryModel = new Model_Directories();
            $postData = $this->getRequest()->getPost();
            if (isset($postData['delete'])) {
                $dirId = $this->getRequest()->getPost('delete');
                $directoryModel->delete(['id = ?' => $dirId]);
                $this->_flashMessenger->addMessage('Folder deleted.', 'success');
            } elseif (isset($postData['saveOrder'])) {
                foreach ($postData['order'] as $dirId => $order) {
                    $directoryModel->update(['order' => $order], ['id = ?' => $dirId]);
                }
                $this->_flashMessenger->addMessage('Folder order has been updated.', 'success');
            }
        }

        $this->_redirect($this->view->url(['action' => 'overview']));
    }

    /**
     * Add hidden elements to form to allow for creating relations
     * @param array     $relTids  An array of related inputIds
     * @param Zend_Form $form    The form object
     */
    protected function addNewElements($relTids, Zend_Form $form)
    {
        $relTID = $form
            ->createElement('hidden', 'rel_tid')
            ->setValue(implode(',', $relTids));
        $form->addElement($relTID);

        $kid = $form
            ->createElement('hidden', 'kid')
            ->setValue($this->_consultation['kid']);
        $form->addElement($kid);
    }

    /**
     * Checks the kid and returns the values from DB if the consultation exists
     * @param get param kid
     * @return variables from consultation or votingprepare error
     */
    protected function getKid($params)
    {
        if (isset($params["kid"])) {
            $isDigit = new Zend_Validate_Digits();

            if ($params["kid"] > 0 && $isDigit->isValid($params["kid"])) {
                $consultationModel = new Model_Consultations();
                $this->_consultation = $consultationModel->getById($params["kid"]);
                if (count($this->_consultation) == 0) {
                    $this->_flashMessenger->addMessage('There is no consultation with this ID.', 'error');
                    $this->_redirect('/admin/votingprepare/error');
                } else {
                    return $this->_consultation;
                }
            } else {
                $this->_flashMessenger->addMessage('Consultation ID is invalid.', 'error');
                $this->_redirect('/admin/votingprepare/error');
            }
        }
    }

    /**
     * Checks the qid
     * @param qid, get param
     * @return (int)
     */
    protected function getQid($params)
    {
        if (isset($params["qid"])) {
            $isDigit = new Zend_Validate_Digits();
            if ($params["qid"] > 0 && $isDigit->isValid($params["qid"])) {
                return (int) $params["qid"];
            } else {
                $this->_flashMessenger->addMessage('Question ID is invalid.', 'error');
                $this->_redirect('/admin/votingprepare/error');
            }
        }
    }

    /**
     * Checks the tid
     * @param tid, get params
     * @return (int)
     */
    protected function getTId($params)
    {
        if (isset($params["tid"])) {
            $isDigit = new Zend_Validate_Digits();
            if ($params["tid"] > 0 && $isDigit->isValid($params["tid"])) {
                return (int) $params["tid"];
            } else {
                $this->_flashMessenger->addMessage('Thesis ID is invalid.', 'error');
                $this->_redirect('/admin/votingprepare/error');
            }
        }
    }
}
