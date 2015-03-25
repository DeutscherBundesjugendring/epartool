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

    public function partnerIndexAction()
    {
        $partnerModel = new Model_Partner();
        $form = new Admin_Form_ListControl();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                if (isset($data['delete'])) {
                    $partnerModel->delete(['id=?' => $data['delete']]);
                    $this->_flashMessenger->addMessage('Partner was deleted.', 'success');
                } elseif (isset($data['saveOrder'])) {
                    $db = $partnerModel->getAdapter();
                    $db->beginTransaction();
                    try {
                        foreach ($data['order'] as $partnerId => $order) {
                            $partnerModel->update(['order' => $order], ['id = ?' => $partnerId]);
                        }
                        $db->commit();
                        $this->_flashMessenger->addMessage('Order was updated.', 'success');
                    } catch (Exception $e) {
                        $db->rollback();
                        throw $e;
                    }
                    $this->_redirect('/admin/settings/partner-index');
                }
            } else {
                $this->_flashMessenger->addMessage('Form invalid', 'error');
            }
        }

        $this->view->form = $form;
        $this->view->partners = $partnerModel->fetchAll($partnerModel->select()->order('order ASC'));
    }

    public function partnerEditAction()
    {
        $partnerModel = new Model_Partner();
        $form = new Admin_Form_Partner();
        $partnerId = $this->getRequest()->getParam('id');

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                unset($data['submit']);
                unset($data['csrf_token_partner']);

                $db = $partnerModel->getAdapter();
                $db->beginTransaction();
                try {
                    if ($data['id']) {
                        $partnerModel->update($data, ['id=?' => $data['id']]);
                        $partnerId = $data['id'];
                    } else {
                        $proj = Zend_Registry::get('systemconfig')->project;
                        $partnerId = $partnerModel->insert(array_merge($data, ['proj' => $proj]));
                    }
                    $db->commit();
                    $this->_flashMessenger->addMessage('Partners were saved.', 'success');
                    $this->_redirect('/admin/settings/partner-edit/id/' . $partnerId);
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            } else {
                $this->_flashMessenger->addMessage('Form invalid', 'error');
            }
        } else {
            $partner = $partnerModel->find($partnerId)->current();
            if ($partner) {
                $form->populate($partner->toArray());
            } elseif ($partnerId !== null) {
                $this->redirect('/admin/settings/partner-index');
            }

        }

        $this->view->form = $form;
    }
}
