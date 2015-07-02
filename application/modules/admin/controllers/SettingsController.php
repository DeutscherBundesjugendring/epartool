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

    public function indexAction()
    {
        $paramModel = new Model_Parameter();
        $params = $paramModel->getAsArray();
        $form = new Admin_Form_Settings();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $proj = Zend_Registry::get('systemconfig')->project;
                $db = $paramModel->getAdapter();
                $db->beginTransaction();
                try {
                    foreach ($data as $field => $value) {
                        $paramModel->update(
                            ['value' => $value],
                            ['name=?' => str_replace('_', '.', $field), 'proj=?' => $proj]
                        );
                    }
                    $db->commit();
                    $this->_flashMessenger->addMessage('Settings were saved.', 'success');
                    $this->_redirect('/admin/settings');
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            } else {
                $this->_flashMessenger->addMessage('Form invalid', 'error');
            }
        } else {
            $form->populate($params);
        }

        $this->view->form = $form;
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

    public function footerAction()
    {
        $footerModel = new Model_Footer();
        $footers = $footerModel->fetchAll($footerModel->select()->order('id ASC'));
        foreach ($footers as $footer) {
            $footersArr['footer' . $footer->id] = $footer->text;
        }
        $form = new Admin_Form_Footer($footersArr);


        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $db = $footerModel->getAdapter();
                $db->beginTransaction();
                try {
                    foreach ($footers as $footer) {
                        $footerModel->update(['text' => $data['footer' . $footer->id]], ['id = ?' => $footer->id]);
                    }
                    $db->commit();
                    $this->_flashMessenger->addMessage('Footer was updated.', 'success');
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
                $this->_redirect('/admin/settings/footer');
            } else {
                $this->_flashMessenger->addMessage('Form invalid', 'error');
            }
        } else {
            $form->populate($footersArr);
        }

        $this->view->form = $form;
    }
}
