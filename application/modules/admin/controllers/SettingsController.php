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
        $form = new Admin_Form_Settings_Site();
        $projectCode = Zend_Registry::get('systemconfig')->project;

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $db = $paramModel->getAdapter();
                $db->beginTransaction();
                try {
                    foreach ($data as $field => $value) {
                        if ($field === 'locale') {
                            (new Model_Projects())->update(['locale' => $value], ['proj = ?' => $projectCode]);
                            continue;
                        }

                        $paramModel->update(
                            ['value' => $value],
                            ['name=?' => str_replace('_', '.', $field), 'proj=?' => $projectCode]
                        );
                    }
                    $db->commit();
                    $this->_flashMessenger->addMessage('Settings were saved.', 'success');
                    $this->redirect('/admin/settings');
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            } else {
                $this->_flashMessenger->addMessage('Form invalid', 'error');
            }
        } else {
            $form->populate(array_merge($paramModel->getAsArray(), ['locale' => (new Model_Projects())->find($projectCode)->current()['locale']]));
        }

        $this->view->form = $form;
    }

    public function helpTextIndexAction()
    {
        $helpTextModel = new Model_HelpText();
        $helpTexts = $helpTextModel->fetchAll(
            $helpTextModel
                ->select()
                ->where('project_code = ?', Zend_Registry::get('systemconfig')->project)
                ->where('module = ?', 'default')
                ->from($helpTextModel->info(Model_HelpText::NAME), ['id', 'name'])
        );

        $this->view->helpTexts = $helpTexts;
        
        $helpTextAdminModel = new Model_HelpText();
        $helpTextsAdmin = $helpTextAdminModel->fetchAll(
            $helpTextModel
                ->select()
                ->where('project_code = ?', Zend_Registry::get('systemconfig')->project)
                ->where('module = ?', 'admin')
                ->from($helpTextModel->info(Model_HelpText::NAME), ['id', 'name'])
        );
        $this->view->helpTextsAdmin = $helpTextsAdmin;
    }

    public function helpTextEditAction()
    {
        $helpTextModel = new Model_HelpText();
        $helpTextId = $this->getRequest()->getParam('id');
        $helpText = $helpTextModel->fetchRow(
            $helpTextModel
                ->select()
                ->where('project_code = ?', Zend_Registry::get('systemconfig')->project)
                ->where('id = ?', $helpTextId)
        );

        if (!count($helpText)) {
            $this->_helper->redirector('help-text-index');
        }

        $form = new Admin_Form_HelpText();
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                $helpTextModel->update(['body' => $postData['body']], ['id=?' => $helpText->id]);
                $this->_flashMessenger->addMessage('Help text was saved.', 'success');
                $this->_redirect('/admin/settings/help-text-edit/id/' . $helpTextId);
            } else {
                $this->_flashMessenger->addMessage('Form invalid', 'error');
            }
        } else {
            $form->populate($helpText->toArray());
        }

        $this->view->form = $form;
        $this->view->helpTextTitle = Model_HelpText::getTranslatedName($helpText->name);
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

    public function votingAction()
    {
        $projectModel = new Model_Projects();
        $projectCode = Zend_Registry::get('systemconfig')->project;
        $form = new Admin_Form_Settings_Voting();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $db = $projectModel->getAdapter();
                $db->beginTransaction();
                try {
                    $projectModel->update(['vot_q' => $data['voting_question']], ['proj=?' => $projectCode]);
                    $db->commit();
                    $this->_flashMessenger->addMessage('Settings were saved.', 'success');
                    $this->redirect('/admin/settings/voting');
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            } else {
                $this->_flashMessenger->addMessage('Form invalid', 'error');
            }
        } else {
            $form->populate(['voting_question' => $projectModel->find($projectCode)->current()->vot_q]);
        }

        $this->view->form = $form;
    }

    public function servicesAction()
    {
        $projectModel = new Model_Projects();
        $projectCode = Zend_Registry::get('systemconfig')->project;
        $form = new Admin_Form_Settings_Services();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $db = $projectModel->getAdapter();
                $db->beginTransaction();
                try {
                    $projectModel->update([
                        'video_facebook_enabled' => $data['video_facebook_enabled'],
                        'video_youtube_enabled' => $data['video_youtube_enabled'],
                        'video_vimeo_enabled' => $data['video_vimeo_enabled'],
                    ], ['proj=?' => $projectCode]);
                    $db->commit();
                    $this->_flashMessenger->addMessage('Your services settings were updated.', 'success');
                    $this->redirect('/admin/settings/services');
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            } else {
                $this->_flashMessenger->addMessage(
                    'Services settings could not be updated. Please check the errors marked in the form below and try again.',
                    'error'
                );
            }
        } else {
            $form->populate($projectModel->find($projectCode)->current()->toArray());
        }

        $this->view->form = $form;
    }
    
    public function lookAndFeelAction()
    {
        $projectModel = new Model_Projects();
        $projectCode = Zend_Registry::get('systemconfig')->project;

        $form = new Admin_Form_Settings_LookAndFeel();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $db = $projectModel->getAdapter();
                $db->beginTransaction();
                $data = [];
                if (!empty($formData['theme_id'])) {
                    $data['theme_id'] = $formData['theme_id'];
                    $data['color_accent_1'] = $data['color_primary'] = $data['color_accent_2'] = null;
                } else {
                    $data['theme_id'] = null;
                    $data['color_accent_1'] = mb_substr($formData['color_accent_1'], 1);
                    $data['color_primary'] = mb_substr($formData['color_primary'], 1);
                    $data['color_accent_2'] = mb_substr($formData['color_accent_2'], 1);
                }
                
                if (!empty($formData['logo'])) {
                    $data['logo'] = $formData['logo'];
                }
                
                if (!empty($formData['favicon'])) {
                    $data['favicon'] = $formData['favicon'];
                }
                
                if (!empty($formData['mitmachen_bubble'])) {
                    $data['mitmachen_bubble'] = true;
                } else {
                    $data['mitmachen_bubble'] = false;
                }
                
                try {
                    $projectModel->update($data, ['proj=?' => $projectCode]);
                    $db->commit();
                    $this->_flashMessenger->addMessage('Theme was updated.', 'success');
                    $this->redirect('/admin/settings/look-and-feel');
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            } else {
                $this->_flashMessenger->addMessage(
                    'Theme settings cannot be updated. Please check the errors marked in the form below and try again.',
                    'error'
                );
            }
        } else {
            $data = $projectModel->find($projectCode)->current()->toArray();
            if (!empty($data['theme_id'])) {
                $theme = (new Model_Theme())->find($data['theme_id'])->current();
                $data['color_accent_1'] = $theme['color_accent_1'];
                $data['color_primary'] = $theme['color_primary'];
                $data['color_accent_2'] = $theme['color_accent_2'];
            }
            $form->populate($data);
        }

        $this->view->form = $form;
    }

    public function finishContributionAction()
    {
        $projectModel = new Model_Projects();
        $projectCode = Zend_Registry::get('systemconfig')->project;

        $form = new Admin_Form_Settings_FinishContribution();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $db = $projectModel->getAdapter();
                $db->beginTransaction();
                try {
                    $projectModel->update(
                        [
                            'state_label' => !empty($formData['state_label']) ? $formData['state_label'] : null,
                            'license' => $formData['license'],
                        ],
                        ['proj=?' => $projectCode]
                    );
                    $db->commit();
                    $this->_flashMessenger->addMessage('Form settings was updated.', 'success');
                    $this->redirect('/admin/settings/finish-contribution');
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            } else {
                $this->_flashMessenger->addMessage(
                    'Form settings cannot be updated. Please check the errors marked in the form below and try again.',
                    'error'
                );
            }
        } else {
            $form->populate($projectModel->find($projectCode)->current()->toArray());
        }

        $this->view->form = $form;
    }
}
