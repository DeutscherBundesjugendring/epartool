<?php

class Admin_SettingsController extends Zend_Controller_Action
{
    /**
     * Holds a FlashMessenger instance
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    private $_flashMessenger = null;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->initView();
    }

    public function helpTextIndexAction()
    {
        $helpTextModel = new Model_HelpText();
        $helpTexts = $helpTextModel->fetchAll(
            $helpTextModel
                ->select()
                ->from($helpTextModel->info(Model_HelpText::NAME), ['id', 'name'])
        );

        $this->view->helpTexts = $helpTexts;
    }

    public function helpTextEditAction()
    {
        $helpTextModel = new Model_HelpText();
        $helpTextId = $this->getRequest()->getParam('id');
        $helpText = $helpTextModel->find($helpTextId);

        if (!count($helpText)) {
            $this->_helper->redirector('help-text-index');
        }

        $form = new Admin_Form_HelpText();
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                $helpTextModel->update(['body' => $postData['body']], ['id=?' => $helpTextId]);
                $this->_flashMessenger->addMessage('Help text was saved.', 'success');
                $this->_redirect('/admin/settings/help-text-edit/id/' . $helpTextId);
            } else {
                $this->_flashMessenger->addMessage('Form invalid', 'error');
            }
        } else {
            $form->populate($helpText->toArray()[0]);
        }

        $this->view->form = $form;
        $this->view->helpTextTitle = Model_HelpText::getTranslatedName($helpText[0]->name);
    }
}
