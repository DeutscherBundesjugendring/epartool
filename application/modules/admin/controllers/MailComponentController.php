<?php

class Admin_MailComponentController extends Zend_Controller_Action
{
    /**
     * Holds a FlashMessanger helper instance for this controller
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $_flashMessenger;

    /**
     * Holds the instantiated template model object
     * @var Model_Mail_Template
     */
    protected $_componentModel;

    /**
     * Initializes this controller
     */
    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_componentModel = new Model_Mail_Component();
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    /**
     * Displays a list of all email component in system
     */
    public function indexAction()
    {
        $this->view->form = new Admin_Form_ListControl();
        $this->view->components = $this->_componentModel->fetchAll(
            $this->_componentModel->select()
        );
    }

    /**
     * Creates new or updates existing email component
     */
    public function detailAction()
    {
        $form = new Admin_Form_Mail_Component();
        $componentId = $this->getRequest()->getParam('id');

        if (!empty($componentId)) {
            $component = $this->_componentModel->find($componentId)->current();
            $form->getElement('name')->removeValidator('Db_NoRecordExists');
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                if (!empty($componentId)) {
                    $this->_componentModel->update(
                        $component->setFromArray($values)->toArray(),
                        array('id=?' => $component->id)
                    );
                } else {
                    $componentId = $this->_componentModel->insert($values);
                }
                $this->_flashMessenger->addMessage('Component saved.', 'success');
                $this->_redirect('/admin/mail-component/detail/id/' . $componentId);
            } else {
                $this->_flashMessenger->addMessage('Form is not valid!', 'error');
            }
        } elseif (isset($component)) {
            $form->populate($component->toArray());
        }
        $this->view->form = $form;
        $this->view->componentId = $componentId;
        $placeholderModel = new Model_Mail_Placeholder();
        $this->view->placeholders = $placeholderModel->fetchAll($placeholderModel->select()->where('is_global=?', 1));
    }

    /**
     * Deletes the specified template
     */
    public function deleteAction()
    {
        $form = new Admin_Form_ListControl();
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            if ($form->isValid($values)) {
                $componentId = $this->getRequest()->getPost('deleteId');
                if ($this->_componentModel->delete(array('id=?' => $componentId))) {
                    $this->_flashMessenger->addMessage('Component deleted', 'success');
                } else {
                    $this->_flashMessenger->addMessage('Component delete error', 'error');
                }
            }
        }

        $this->_helper->redirector('index');
    }
}
