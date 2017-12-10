<?php

class Admin_VotingprepareController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_consultation = null;
    protected $_qid;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $params = $this->_request->getParams();
        $this->_consultation = $this->_helper->consultationGetter($params);
        $this->_qid = isset($params['qid']) ? $params['qid'] : null;
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
        if (!$this->_qid || !isset($this->_consultation['questions'][$this->_qid])) {
            throw new Zend_Controller_Action_Exception('Question ID is invalid.', 404);
        }

        $form = new Admin_Form_ListControl();
        $wheres['inpt.qi = ?'] = $this->_qid;
        $fulltext = $this->getRequest()->getParam('fulltext', null);
        if ($fulltext) {
            $wheres['thes LIKE ? OR expl LIKE ?'] = '%' . $fulltext . '%';
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
            if ($this->getRequest()->getPost('unlink', null)) {
                $ids = $this->getRequest()->getPost('unlink', null);
                $ids = explode('-', $ids);
                $inputModel->unlinkById($ids[0], $ids[1]);
                $this->_flashMessenger->addMessage('Contribution has been removed from related.', 'success');
            } elseif ($this->getRequest()->getPost('delete', null)) {
                $inputModel->deleteById($this->getRequest()->getPost('delete', null));
                $this->_flashMessenger->addMessage('Contribution has been deleted.', 'success');
            } elseif ($inputIds) {
                if ($this->getRequest()->getPost('releaseBulk', null)) {
                    $count = $inputModel->editBulk($inputIds, ['is_confirmed' => true]);
                    $msg = sprintf($this->view->translate('%d contributions have been released.'), $count);
                } elseif ($this->getRequest()->getPost('blockBulk', null)) {
                    $count = $inputModel->editBulk($inputIds, ['is_confirmed' => false]);
                    $msg = sprintf($this->view->translate('%d contributions have been blocked.'), $count);
                } elseif ($this->getRequest()->getPost('enableVotingBulk', null)) {
                    $count = $inputModel->editBulk($inputIds, ['is_votable' => true]);
                    $msg = sprintf($this->view->translate('%d contributions have been sent to voting.'), $count);
                } elseif ($this->getRequest()->getPost('blockVotingBulk', null)) {
                    $count = $inputModel->editBulk($inputIds, ['is_votable' => false]);
                    $msg = sprintf($this->view->translate('%d contributions have been removed from voting.'), $count);
                } elseif ($this->getRequest()->getPost('deleteBulk', null)) {
                    $count = $inputModel->deleteBulk($inputIds);
                    $msg = sprintf($this->view->translate('%d contributions have been deleted.'), $count);
                } elseif ($this->getRequest()->getPost('sendToDictionaryBulk', null)) {
                    $dirId = $this->getRequest()->getPost('sendToDictionaryId');
                    $dirId = $dirId ? $dirId : null;
                    $count = $inputModel->update(['dir' => $dirId], ['tid IN (?)' => $inputIds]);
                    $msg = sprintf($this->view->translate('%d contributions have been moved.'), $count);
                } elseif ($this->getRequest()->getPost('merge', null)) {
                    // This is awkward, but we need to utilise the same checkboxes as for bulk editing actions
                    // Essentially this only takes values from inputIds checkboxes and constructs an url to redirect to
                    $this->redirect($this->view->url([
                        'action' => 'merge',
                        'inputIds' => $this->getRequest()->getPost('inputIds', null)
                    ]), ['prependBase' => false]);
                }
                if(isset($msg)) {
                    $this->_flashMessenger->addMessage($msg, 'success');
                }
            }
        }

        $this->redirect($this->view->url(['action' => 'overview']), ['prependBase' => false]);
    }

    /**
     * Inserts a new input from admin and set the old input as its child
     */
    public function splitAction()
    {
        $inputModel = new Model_Inputs();
        $form = new Admin_Form_Input(
            $this->view->url(['action' => 'overview']),
            Admin_Form_Input::AFTER_SUBMIT_SPLIT_NEXT
        );
        $form->getElement('qi')->setAttrib('disabled', 'disabled');

        $origInputId = $this->getRequest()->getParam('inputId');
        $origInputData = $inputModel->find($origInputId)->current();

        $projectSettings = (new Model_Projects())->find(Zend_Registry::get('systemconfig')->project)->current();
        $question = (new Model_Questions())->find($origInputData['qi'])->current();
        $form->setVideoEnabled(
            $question['video_enabled']
            && ($projectSettings['video_facebook_enabled']
                || $projectSettings['video_youtube_enabled']
                || $projectSettings['video_vimeo_enabled']
            )
        );

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $postData['qi'] = $origInputData['qi'];
            if ($form->isValid($postData)) {
                $newInputId = $inputModel->addInputs($postData);
                $inputModel->appendRelIds($origInputId, [$newInputId]);
                $this->_flashMessenger->addMessage('New contribution has been created.', 'success');
                $this->redirect($this->view->url(), ['prependBase' => false]);
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
            }
        } else {
            $form->populate([
                'qi' => $origInputData['qi'],
                'is_confirmed_by_user' => $origInputData['is_confirmed_by_user'],
                'is_confirmed' => $origInputData['is_confirmed'],
                'is_votable' => true,
                'latitude' => $origInputData['latitude'],
                'longitude' => $origInputData['longitude'],
            ]);
        }

        $this->view->inputs = $inputModel->fetchAllInputs(['tid = ?' => $origInputId]);
        $this->view->consultation = $this->_consultation;
        $this->view->form = $form;
    }

    /**
     * Inserts a new input from admin and set the old inputs as childern
     */
    public function mergeAction()
    {
        $inputModel = new Model_Inputs();
        $origInputIds = $this->getRequest()->getParam('inputIds');
        $form = new Admin_Form_Input($this->view->url(['action' => 'overview', 'inputIds' => null]));

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                $newInputId = $inputModel->addInputs($postData);
                foreach ($origInputIds as $origInputId) {
                    $inputModel->appendRelIds($origInputId, [$newInputId]);
                }
                $this->_flashMessenger->addMessage('New contribution has been created.', 'success');
                $this->redirect(
                    $this->view->url(['action' => 'overview', 'inputIds' => null]),
                    ['prependBase' => false]
                );
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
            }
        } else {
            $this->_flashMessenger->addMessage(
                'Video contribution settings are inherited from Question, therefore it is possible to add a video only after saving this Contribution thus linking it to a Question.',
                'info'
            );
        }

        $this->view->inputs = $inputModel->fetchAllInputs(['tid IN (?)' => $origInputIds]);
        $this->view->consultation = $this->_consultation;
        $this->view->form = $form;
    }

    /**
     * Creates a copy of an input
     */
    public function copyAction()
    {
        $inputModel = new Model_Inputs();
        $origInputId = $this->getRequest()->getParam('inputId');
        $origData = $inputModel->getById($origInputId);
        $form = new Admin_Form_Input($this->view->url(['action' => 'overview']));

        $projectSettings = (new Model_Projects())->find(Zend_Registry::get('systemconfig')->project)->current();
        $question = (new Model_Questions())->find($origData['qi'])->current();
        $form->setVideoEnabled(
            $question['video_enabled']
            && ($projectSettings['video_facebook_enabled']
                || $projectSettings['video_youtube_enabled']
                || $projectSettings['video_vimeo_enabled']
            )
        );


        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                $newInputId = $inputModel->addInputs($postData);
                $inputModel->appendRelIds($origInputId, [$newInputId]);
                $this->_flashMessenger->addMessage('Contribution has been copied. This is the copy.', 'success');
                $this->redirect(
                    $this->view->url([
                        'controller' => 'input',
                        'action' => 'edit',
                        'tid' => $origInputId,
                        'return' => 'votingprepare',
                    ]),
                    ['prependBase' => false]
                );
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
            }
        } else {
            $origData['uid'] = null;
            unset($origData['when']);
            unset($origData['tags']);
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
                $this->redirect($this->view->url(), ['prependBase' => false]);
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

        $this->redirect($this->view->url(['action' => 'overview']), ['prependBase' => false]);
    }

    public function addRelatedAction()
    {
        $inputId = $this->getRequest()->getParam('inputId');
        $inputModel = new Model_Inputs();
        $originalInput = $inputModel->find($inputId)->current();
        if (!$originalInput) {
            throw new Zend_Controller_Action_Exception(sprintf('Contribution ID %d not found.', $inputId), 404);
        }

        $form = new Admin_Form_ListControl();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['inputIds'])) {
                $inputModel->appendRelIds($inputId, $postData['inputIds']);
                $this->_flashMessenger->addMessage('Selected contributions were added to related.', 'success');
                $this->redirect($this->view->url(['action' => 'overview']), ['prependBase' => false]);
            } else {
                $this->_flashMessenger->addMessage('No contributions were selected.', 'error');
            }
        }

        $this->view->input = $originalInput;
        $this->view->form = $form;
        $this->view->inputs = $inputModel->fetchAllInputs(['inpt.qi = ?' => $originalInput->qi]);
        $this->view->consultation = $this->_consultation;
    }
}
