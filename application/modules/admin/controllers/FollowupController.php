<?php

class Admin_FollowupController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_consultation = null;
    private $_kid;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $kid = $this->_request->getParam('kid', 0);
        if ($kid) {
            $consultationModel = new Model_Consultations();
            $this->_consultation = $consultationModel->find($kid)->current();
            $this->view->consultation = $this->_consultation;
            $this->_kid = $kid;
        }
    }

    /*
     * Displayes a list of followups for the given consultation
     */
    public function indexAction()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $followupFiles = new Model_FollowupFiles();
        $this->view->followupFiles = $followupFiles->getByKid($kid, 'when DESC');
    }

    /*
     * Creates a new snippet
     */
    public function createSnippetAction()
    {
        $ffid = $this->getRequest()->getParam('ffid', 0);
        $form = new Admin_Form_Followup_Snippet();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $followup = (new Model_Followups())->createRow($form->getValues());
                $followup->ffid = $ffid;
                $newId = $followup->save();

                $followups = (new Model_FollowupFiles())->getFollowupsById($ffid, 'docorg ASC');
                $i = 1;
                foreach ($followups as $followup) {
                    $followup->docorg = $i++;
                    $followup->save();
                }

                $this->_flashMessenger->addMessage('The snippet was created successfully.', 'success');
                $this->_redirect($this->view->url(['action' => 'edit-snippet', 'fid' => $newId]));
            } else {
                $this->_flashMessenger->addMessage('Form invalid.', 'error');
            }
        }

        $this->view->form = $form;
        $this->view->pageTitle = $this->view->translate('Create Followup Snippet');
        $this->render('snippet-detail');
    }

    /*
     * Edits a snippet
     */
    public function editSnippetAction()
    {
        $fid = $this->getRequest()->getParam('fid', null);
        $snippetModel = new Model_Followups();
        $form = new Admin_Form_Followup_Snippet();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($params)) {
                $snippetModel
                    ->find($fid)
                    ->current()
                    ->setFromArray($form->getValues())
                    ->save();

                $this->_flashMessenger->addMessage('The followup snippet was saved successfully.', 'success');
                $this->_redirect($this->view->url());
            } else {
                $this->_flashMessenger->addMessage('Form invalid.', 'error');
            }
        } else {
            $snippet = $snippetModel->find($fid)->current();
            $form->populate($snippet->toArray());
        }

        // This is here because all data in db is saved escaped :(
        foreach ($form->getElements() as $element) {
            $element->clearFilters();
            $element->setValue(html_entity_decode($element->getValue()));
        }

        $this->view->form = $form;
        $this->view->pageTitle = $this->view->translate('Edit Followup Snippet');
        $this->render('snippet-detail');
    }

    /*
     * Creates new followup
     */
    public function createFollowupAction()
    {
        $form = new Admin_Form_Followup_File();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $followup = (new Model_FollowupFiles())->createRow($form->getValues());
                $followup->kid = $this->_kid;
                $newId = $followup->save();

                $this->_flashMessenger->addMessage('The followup was created successfully.', 'success');
                $this->_redirect($this->view->url(['action' => 'edit-followup', 'ffid' => $newId]));
            } else {
                $this->_flashMessenger->addMessage('Form ivalid', 'error');
            }
        }

        $this->view->form = $form;
        $this->view->pageTitle = $this->view->translate('Create Followup');
        $this->render('followup-detail');
    }

    /*
     * Edits a followup
     */
    public function editFollowupAction()
    {
        $ffid = $this->getRequest()->getParam('ffid', null);
        $followupModel = new Model_FollowupFiles();
        $form = new Admin_Form_Followup_File();
        $form->setKid($this->_kid);

        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            if ($form->isValid($params)) {
                $followupModel
                    ->find($ffid)
                    ->current()
                    ->setFromArray($form->getValues())
                    ->save();

                $this->_flashMessenger->addMessage('The followup was saved successfully.', 'success');
                $this->_redirect($this->view->url());
            } else {
                $this->_flashMessenger->addMessage('Form invalid.', 'error');
                $followupFile = $params;
            }
        } else {
            $followup = $followupModel->find($ffid)->current();
            $form->populate($followup->toArray());
        }

        // This is here because all data in db is saved escaped :(
        foreach ($form->getElements() as $element) {
            $element->clearFilters();
            $element->setValue(html_entity_decode($element->getValue(), ENT_COMPAT, 'UTF-8'));
        }

        $this->view->form = $form;
        $this->view->pageTitle = $this->view->translate('Edit Followup');
        $this->render('followup-detail');
    }

    /**
     * Displays the list fo snippets for the given followup
     */
    public function snippetsAction()
    {
        $ffid = $this->getRequest()->getParam('ffid', 0);
        $form = new Admin_Form_ListControl();

        $snippetModel = new Model_Followups();
        $followupRefModel = new Model_FollowupsRef();
        $followupModel = new Model_FollowupFiles();

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                foreach ($postData['docorg'] as $snippetId => $docorg) {
                    $a = $snippetModel
                        ->find($snippetId)
                        ->current()
                        ->setFromArray(['docorg' => $docorg])
                        ->save();
                    var_dump($a);
                }
                $this->redirect($this->view->url());
            }
        }

        $snippets = array();
        $res = $followupModel->getFollowupsById($ffid, 'docorg ASC')->toArray();
        foreach ($res as $followup) {
            $rel = $snippetModel->getRelated($followup['fid']);
            $reltothis = $followupRefModel->getRelatedFollowupByFid($followup['fid']);
            $snippet = $followup;
            $snippet['relFowupCount'] = $rel['count'];
            $snippet['reltothisFowupCount'] = count($reltothis);
            $snippets[] = $snippet;
        }

        $this->view->snippets = $snippets;
        $this->view->ffid = $ffid;
        $this->view->snippetTypes = Model_Followups::getTypes();
        $this->view->hlvl = Model_Followups::getHierarchyLevels();
        $this->view->listControlForm = $form;
    }

    /*
     * Deletes a followup
     */
    public function deleteFollowupAction()
    {
        $ffid = $this->getRequest()->getParam('ffid', 0);

        (new Model_FollowupFiles())
            ->find($ffid)
            ->current()
            ->delete();

        $this->_flashMessenger->addMessage('The followup was deleted successfully.', 'success');
        $this->_redirect($this->view->url(['action' => 'index', 'ffid' => null]));
    }

    /*
     * Delete a snippet
     */
    public function deleteSnippetAction()
    {
        $fid = $this->getRequest()->getParam('fid', null);
        $ffid = $this->getRequest()->getParam('ffid', null);

        (new Model_Followups())
            ->find($fid)
            ->current()
            ->delete();

        $this->_flashMessenger->addMessage('The snippet was deleted successfully.', 'success');
        $this->_redirect($this->view->url(['action' => 'snippets', 'ffid' => $ffid]));
    }

    /**
     * Displays and edits snippet links
     */
    public function snippetReferenceAction()
    {
        $snippetId = $this->getRequest()->getParam('fid', null);
        $followupId = $this->getRequest()->getParam('ffid', null);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $followupRefModel = new Model_FollowupsRef();

            if (!empty($postData['link_inputs'])) {
                $inserted = $followupRefModel->insertBulk(
                    !empty($postData['input_links']) ? $postData['input_links'] : [],
                    $snippetId,
                    'tid'
                );
                $message = sprintf($this->view->translate('%d Inputs were liked'), $inserted);
                $this->_flashMessenger->addMessage($message, 'success');
            }
            if (!empty($postData['followup_links']) && !empty($postData['link_followups'])) {
                $inserted = $followupRefModel->insertBulk(
                    !empty($postData['followup_links']) ? $postData['followup_links'] : [],
                    $snippetId,
                    'ffid'
                );
                $message = sprintf($this->view->translate('%d Followups were liked'), $inserted);
                $this->_flashMessenger->addMessage($message, 'success');
            }
            if (!empty($postData['link_snippets'])) {
                $inserted = $followupRefModel->insertBulk(
                    isset($postData['snippet_links']) ? $postData['snippet_links'] : [],
                    $snippetId,
                    'fid'
                );
                $message = sprintf($this->view->translate('%d Snippets were liked'), $inserted);
                $this->_flashMessenger->addMessage($message, 'success');
            }
            $this->redirect($this->view->url());
        }

        $snippetModel = new Model_Followups();
        $followupModel = new Model_FollowupFiles();
        $questionModel = new Model_Questions();

        $questions = $questionModel->getWithInputs(
            [$questionModel->info(Model_Questions::NAME) . '.kid = ?' => $this->_kid]
        );

        $followups = $followupModel->getWithSnippets(
            [
                $snippetModel->info(Model_Followups::NAME) . '.fid != ? OR '
                    . $snippetModel->info(Model_Followups::NAME) . '.fid IS NULL' => $snippetId,
                $followupModel->info(Model_FollowupFiles::NAME) . '.kid = ?' => $this->_kid,
            ]
        );

        $related = [];
        $relatedRaw = $snippetModel->getRelated($snippetId);
        foreach ($relatedRaw['snippets'] as $snippet) {
            $related['snippets'][$snippet['fid']] = true;
        }
        foreach ($relatedRaw['inputs'] as $input) {
            $related['inputs'][$input['tid']] = true;
        }
        foreach ($relatedRaw['followups'] as $followup) {
            $related['followups'][$followup['ffid']] = true;
        }

        $this->view->questions = $questions;
        $this->view->followups = $followups;
        $this->view->related = $related;
        $this->view->followupId = $followupId;
    }
}
