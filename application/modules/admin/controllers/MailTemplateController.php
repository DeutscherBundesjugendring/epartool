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
        $form = new Admin_Form_Mail_Template();
        $templateId = $this->getRequest()->getParam('id');

        if ($templateId) {
            $template = $this->_templateModel->find($templateId)->current();
            $templateTypeName = $template->findModel_Mail_Template_Type()->current()->name;
            $isSystem = $templateTypeName === Model_Mail_Template_Type::TEMPLATE_TYPE_SYSTEM;
            if ($template->name === $this->getRequest()->getPost('name')) {
                $form->getElement('name')->removeValidator('Db_NoRecordExists');
            }

            if ($isSystem) {
                $form->getElement('name')
                    ->clearValidators()
                    ->setRequired(false)
                    ->setAttrib('disabled', 'disabled');
            }
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                $db = $this->_templateModel->getAdapter();
                $db->beginTransaction();
                try {
                    if (!empty($templateId)) {
                        $dbData = $template->setFromArray($values)->toArray();
                        if ($isSystem) {
                            unset($dbData['name']);
                        }
                        $this->_templateModel->update($dbData, ['id=?' => $template->id]);
                    } else {
                        $templateId = $this->_templateModel->insert($values);
                    }
                    $db->commit();
                    $this->_flashMessenger->addMessage('Email template has been saved.', 'success');
                    $this->_redirect('/admin/mail-template/detail/id/' . $templateId);
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            } else {
                $this->_flashMessenger->addMessage('Form is not valid, please check the values entered.', 'error');
                if (isset($template)) {
                    $form->getElement('name')->setValue($template->name);
                }
            }
        } elseif (isset($template)) {
            if ($templateId) {
                $form->populate($template->toArray());
            }
        }

        $this->view->form = $form;
        $this->view->templateId = $templateId;
        $this->view->components = (new Model_Mail_Component())->fetchAll();
        $this->view->placeholders = (new Model_Mail_Placeholder())->getByTemplateName(isset($template) ? $template->name : '');
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
                $db = $this->_templateModel->getAdapter();
                $db->beginTransaction();
                try {
                    $templateId = $this->getRequest()->getPost('deleteId');
                    $this->_templateModel->delete(['id=?' => $templateId]);
                    $db->commit();
                    $this->_flashMessenger->addMessage('Template has been deleted.', 'success');
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            }
        }

        $this->_helper->redirector('index');
    }
}
