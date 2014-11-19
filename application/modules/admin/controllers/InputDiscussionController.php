<?php
class Admin_InputDiscussionController extends Zend_Controller_Action {

    protected $_flashMessenger = null;
    protected $_consultation = null;


    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_helper->layout->setLayout('backend');
        $kid = $this->_request->getParam('kid', null);
        if ($kid) {
            $this->_consultation = (new Model_Consultations())->getById($kid);
            $this->view->consultation = $this->_consultation;
        }
    }

    /**
     * Lists all inputs related to the given input
     */
    public function indexAction()
    {
        $inputId = $this->getRequest()->getParam('inputId');
        $form = new Admin_Form_ListControl();

        $discModel = new Model_InputDiscussion();
        $discussionContribs = $discModel->fetchAll(
            $discModel
                ->select()
                ->where('input_id=?', $inputId)
                ->order('time_created DESC')
        );

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if ($form->isValid($postData)) {
                if (isset($postData['hide'])) {
                    $contribId = $postData['hide'];
                    $res = $discModel->update(['is_visible' => false], ['id=?' => $contribId]);
                    $message = $this->view->translate('The discussion contribution was hidden');
                } elseif (isset($postData['show'])) {
                    $contribId = $postData['show'];
                    $res = $discModel->update(['is_visible' => true], ['id=?' => $contribId]);
                    $message = $this->view->translate('The discussion contribution was shown');
                } elseif (isset($postData['confirm'])) {
                    $contribId = $postData['confirm'];
                    $res = $discModel->update(['is_user_confirmed' => true], ['id=?' => $contribId]);
                    $message = $this->view->translate('The discussion contribution was set as user confirmed');
                } elseif (isset($postData['unconfirm'])) {
                    $contribId = $postData['unconfirm'];
                    $res = $discModel->update(['is_user_confirmed' => false], ['id=?' => $contribId]);
                    $message = $this->view->translate('The user confirmation was removed from the discussion contribution.');
                }

                $this->_flashMessenger->addMessage($message, 'success');
                $this->_redirect('/admin/input-discussion/index/kid/' . $this->_consultation['kid'] . '/inputId/' . $inputId . '#' . $contribId);
            }
        }

        $this->view->discussionContribs = $discussionContribs;
        $this->view->form = $form;
    }
}
