<?php

class Admin_IndexController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;


    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    /**
     * Shows the list of consultations
     */
    public function indexAction()
    {
        $form = new Admin_Form_ListControl();
        $inputsModel = (new Model_Inputs());
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
            $consultations = (new Model_Consultations())->getWithInputsAndContribs(5, 5);
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

        $this->view->form = $form;
        $this->view->consultations = $consultations;
    }
}
