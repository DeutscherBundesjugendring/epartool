<?php
class Admin_InputDiscussionController extends Zend_Controller_Action {

    protected $_flashMessenger = null;
    protected $_consultation = null;


    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_helper->layout->setLayout('backend');
        $this->_consultation = $this->_helper->consultationGetter($this->_request->getParams());
        $this->view->consultation = $this->_consultation;
    }

    /**
     * Lists all inputs related to the given input
     */
    public function indexAction()
    {
        $inputId = $this->getRequest()->getParam('inputId');
        $qid = $this->getRequest()->getParam('qi');
        $form = new Admin_Form_ListControl();

        $inputModel = new Model_Inputs();
        $discModel = new Model_InputDiscussion();
        $discussionContribs = $discModel->fetchAll(
            $discModel
                ->select()
                ->setIntegrityCheck(false)
                ->from(['d' => $discModel->info(Model_InputDiscussion::NAME)])
                ->joinLeft(
                    ['i' => $inputModel->info(Model_Inputs::NAME)],
                    'i.input_discussion_contrib = d.id',
                    ['childInputId' => 'tid']
                )
                ->where('input_id=?', $inputId)
                ->order('time_created DESC')
        );

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if ($form->isValid($postData)) {
                $db = $discModel->getAdapter();
                $db->beginTransaction();
                try {
                    if (isset($postData['hide'])) {
                        $contribId = $postData['hide'];
                        $res = $discModel->update(['is_visible' => false], ['id=?' => $contribId]);
                        $message = $this->view->translate('The discussion contribution was hidden.');
                    } elseif (isset($postData['show'])) {
                        $contribId = $postData['show'];
                        $res = $discModel->update(['is_visible' => true], ['id=?' => $contribId]);
                        $message = $this->view->translate('The discussion contribution was shown.');
                    } elseif (isset($postData['confirm'])) {
                        $contribId = $postData['confirm'];
                        $res = $discModel->update(['is_user_confirmed' => true], ['id=?' => $contribId]);
                        $message = $this->view->translate('The discussion contribution was set as user confirmed.');
                    } elseif (isset($postData['unconfirm'])) {
                        $contribId = $postData['unconfirm'];
                        $res = $discModel->update(['is_user_confirmed' => false], ['id=?' => $contribId]);
                        $message = $this->view->translate('The user confirmation was removed from the discussion contribution.');
                    } elseif (isset($postData['createInput'])
                        && Zend_Date::now()->isEarlier(new Zend_Date($this->_consultation['inp_to'], Zend_Date::ISO_8601))
                        && Zend_Date::now()->isLater(new Zend_Date($this->_consultation['inp_fr'], Zend_Date::ISO_8601))
                    ) {
                        $contribId = $postData['createInput'];
                        $questionId = $inputModel->fetchRow(
                            $inputModel
                                ->select()
                                ->from($inputModel->info(Model_Inputs::NAME), ['qi'])
                                ->where('tid=?', $inputId)
                        )
                        ->qi;
                        $contrib = $discModel->find($contribId)->current();
                        $newInput = [
                            'qi' => $questionId,
                            'thes' => mb_substr($contrib->body, 0, 330), //330 = max length of input->thes as defined in db
                            'expl' => mb_substr($contrib->body, 331, 2000), //2000 = max length of input->thes as defined in db
                            'uid' => null,
                            'type' => Model_Inputs::TYPE_FROM_DISCUSSION,
                            'input_discussion_contrib' => $contribId,
                        ];
                        $newInputId = $inputModel->add($newInput);

                        $message = sprintf(
                            $this->view->translate('An <a href="%s">input</a> was created from the discussion contribution.'),
                            $this->view->url(
                                [
                                    'module' => 'admin',
                                    'controller' => 'input',
                                    'action' => 'edit',
                                    'kid' => $this->_consultation['kid'],
                                    'qid' => $questionId,
                                    'tid' => $newInputId
                                ],
                                null,
                                true
                            )
                        );
                    }
                    $db->commit();
                    $this->_flashMessenger->addMessage($message, 'success');
                    $this->_redirect('/admin/input-discussion/index/kid/' . $this->_consultation['kid'] . '/inputId/' . $inputId . '#' . $contribId);
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            }
        }

        $this->view->qid = $qid;
        $this->view->discussionContribs = $discussionContribs;
        $this->view->videoServicesStatus = (new Model_Projects())->find(
            (new Zend_Registry())->get('systemconfig')->project
        )->current();
        $this->view->form = $form;
    }
}
