<?php

class Admin_MailTemplateController extends Zend_Controller_Action
{
    /**
     * Holds a FlashMessanger helper instance for this controller
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $_flashMessenger;

    /**
     * Holds the instantiated component model object
     * @var Model_Mail_Component
     */
    protected $_templateModel;

    /**
     * Initializes this controller
     */
    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_templateModel = new Model_Mail_Template();
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    /**
     * Displays a list of all email templates in system
     */
    public function indexAction()
    {
        $this->view->form = new Admin_Form_ListControl();
        $this->view->templates = $this->_templateModel->fetchAll(
            $this->_templateModel->select()
        );
    }

    /**
     * Creates new or updates existing email template
     */
    public function detailAction()
    {
        $form = new Admin_Form_Mail_Component();
        $templateId = $this->getRequest()->getParam('id');

        if (!empty($templateId)) {
            $template = $this->_templateModel->find($templateId)->current();
            $form->getElement('name')->removeValidator('Db_NoRecordExists');
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                if (!empty($templateId)) {
                    $this->_templateModel->update(
                        $template->setFromArray($values)->toArray(),
                        array('id=?' => $template->id)
                    );
                } else {
                    $templateId = $this->_templateModel->insert($values);
                }
                $this->_flashMessenger->addMessage('Änderungen gespeichert.', 'success');
                $this->_redirect('/admin/mail-template/detail/id/' . $templateId);
            } else {
                $this->_flashMessenger->addMessage('Bitte überprüfe die Eingaben!', 'error');
            }
        } elseif (isset($template)) {
            $form->populate($template->toArray());
            if ($template->findModel_Mail_Template_Type()->current()->name !== Model_Mail_Template_Type::TEMPLATE_TYPE_SYSTEM) {
                $form->getElement('name')->setAttrib('disabled', 'disabled');
            }
        }
        $this->view->form = $form;
        $this->view->templateId = $templateId;
        $placeholderModel = new Model_Mail_Placeholder();
        $this->view->placeholders = $placeholderModel->getByTemplateId($templateId);
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
                $templateId = $this->getRequest()->getPost('deleteId');
                if ($this->_templateModel->delete(array('id=?' => $templateId))) {
                    $this->_flashMessenger->addMessage('Template deleted', 'success');
                } else {
                    $this->_flashMessenger->addMessage('Template delete error', 'error');
                }
            }
        }

        $this->_helper->redirector('index');
    }
}
