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
     * Displayes a list of follow-ups for the given consultation
     */
    public function indexAction()
    {
        $followupFiles = new Model_FollowupFiles();
        $this->view->followupFiles = $followupFiles->getByKid($this->_kid, 'when ASC');
        $this->view->form = new Admin_Form_ListControl();
    }

    /*
     * Creates a new snippet
     */
    public function createSnippetAction()
    {
        $kid = $this->getRequest()->getParam('kid', null);
        $ffid = $this->getRequest()->getParam('ffid', null);

        $cancelUrl = $this->view->url(['action' => 'snippets', 'kid' => $kid, 'ffid' => $ffid]);
        $form = new Admin_Form_Followup_Snippet($cancelUrl);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $followups = (new Model_FollowupFiles())->getFollowupsById($ffid, 'docorg ASC');
                $snippetOrder = 1;
                foreach ($followups as $followup) {
                    if ($followup->docorg >= $snippetOrder) {
                        $snippetOrder = $followup->docorg + 1;
                    }
                }
                $followup = (new Model_Followups())->createRow($form->getValues());
                $followup->docorg = $snippetOrder;
                $followup->ffid = $ffid;
                $newId = $followup->save();

                $this->_flashMessenger->addMessage('New snippet has been successfully created.', 'success');
                $isReturnToIndex = (bool) $form->getValue('submitAndIndex');
                if ($isReturnToIndex) {
                    $this->_redirect(
                        $this->view->url(
                            ['action' => 'snippets', 'kid' => $kid, 'ffid' => $ffid, 'fid' => null]
                        ),
                        ['prependBase' => false]
                    );
                } else {
                    $this->_redirect(
                        $this->view->url(['action' => 'edit-snippet', 'fid' => $newId]),
                        ['prependBase' => false]
                    );
                }
            } else {
                $this->_flashMessenger->addMessage('The snippet could not be created.', 'error');
            }
        }

        $this->view->form = $form;
        $this->view->pageTitle = $this->view->translate('Create Follow-Up Snippet');
        $this->render('snippet-detail');
    }

    /*
     * Edits a snippet
     */
    public function editSnippetAction()
    {
        $kid = $this->getRequest()->getParam('kid', null);
        $fid = $this->getRequest()->getParam('fid', null);
        $ffid = $this->getRequest()->getParam('ffid', null);

        $cancelUrl = $this->view->url(['action' => 'snippets', 'kid' => $kid, 'ffid' => $ffid]);
        $form = new Admin_Form_Followup_Snippet($cancelUrl);
        $snippetModel = new Model_Followups();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $snippetModel
                    ->find($fid)
                    ->current()
                    ->setFromArray($form->getValues())
                    ->save();

                $this->_flashMessenger->addMessage('Changes saved.', 'success');
                $isReturnToIndex = (bool) $form->getValue('submitAndIndex');
                if ($isReturnToIndex) {
                    $this->_redirect(
                        $this->view->url(
                            ['action' => 'snippets', 'kid' => $kid, 'ffid' => $ffid, 'fid' => null]
                        ),
                        ['prependBase' => false]
                    );
                } else {
                    $this->_redirect($this->view->url(), ['prependBase' => false]);
                }
            } else {
                $this->_flashMessenger->addMessage('The snippet could not be saved.', 'error');
            }
        } else {
            $snippet = $snippetModel->find($fid)->current();
            $form->populate($snippet->toArray());
        }

        $this->view->form = $form;
        $this->view->pageTitle = $this->view->translate('Edit Follow-Up Snippet');
        $this->render('snippet-detail');
    }

    /*
     * Creates new follow-up
     */
    public function createFollowupAction()
    {
        $form = new Admin_Form_Followup_File($this->_kid);
        $form->setKid($this->_kid);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $followup = (new Model_FollowupFiles())->createRow($form->getValues());
                $followup->kid = $this->_kid;
                $newId = $followup->save();

                (new Service_Notification_FollowUpCreatedNotification())->notify([
                    Service_Notification_FollowUpCreatedNotification::PARAM_CONSULTATION_ID => $this->_kid,
                ]);
                $this->_flashMessenger->addMessage('New follow-up has been successfully created.', 'success');
                $this->_redirect($this->view->url(['action' => 'edit-followup', 'ffid' => $newId]), ['prependBase' => false]);
            } else {
                $this->_flashMessenger->addMessage('The follow-up could not be created.', 'error');
            }
        }

        $this->view->form = $form;
        $this->view->pageTitle = $this->view->translate('Create Follow-up');
        $this->render('followup-detail');
    }

    /*
     * Edits a follow-up
     */
    public function editFollowupAction()
    {
        $ffid = $this->getRequest()->getParam('ffid', null);
        $followupModel = new Model_FollowupFiles();
        $form = new Admin_Form_Followup_File($this->_kid);
        $form->setKid($this->_kid);

        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            if ($form->isValid($params)) {
                $followupModel
                    ->find($ffid)
                    ->current()
                    ->setFromArray($form->getValues())
                    ->save();

                $this->_flashMessenger->addMessage('Changes saved.', 'success');
                $this->_redirect($this->view->url(), ['prependBase' => false]);
            } else {
                $this->_flashMessenger->addMessage('The follow-up could not be saved.', 'error');
                $followupFile = $params;
            }
        } else {
            $followup = $followupModel->find($ffid)->current();
            $form->populate($followup->toArray());
        }

        $this->view->form = $form;
        $this->view->pageTitle = $this->view->translate('Edit Follow-Up');
        $this->render('followup-detail');
    }

    /**
     * Displays the list fo snippets for the given follow-up
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
                if ($this->getRequest()->getPost('delete')) {
                    $snippetModel->delete(['fid = ?' => $this->getRequest()->getPost('delete')]);
                    $this->_flashMessenger->addMessage('The snippet has been deleted.', 'success');
                    $this->_redirect($this->view->url(['action' => 'snippets', 'ffid' => $ffid]), ['prependBase' => false]);
                } elseif ($this->getRequest()->getPost('saveOrder')) {
                    foreach ($postData['docorg'] as $snippetId => $docorg) {
                        $snippetModel->update(['docorg' => $docorg], ['fid = ?' => $snippetId]);
                    }
                    $this->_flashMessenger->addMessage('Snippet order has been updated.', 'success');
                    $this->redirect($this->view->url(), ['prependBase' => false]);
                }
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
        $this->view->form = $form;
    }

    /*
     * Deletes a follow-up if there are no snippets attached to it
     */
    public function deleteFollowupAction()
    {
        $form = new Admin_Form_ListControl();

        if ($form->isValid($this->getRequest()->getPost())) {
            try {
                (new Model_FollowupFiles())
                    ->find($this->getRequest()->getPost('delete'))
                    ->current()
                    ->delete();
                $this->_flashMessenger->addMessage('The follow-up has been deleted.', 'success');
            } catch (Dbjr_Exception $e) {
                $this->_flashMessenger->addMessage('The follow-up could not be deleted as there are snippets attached to it.', 'error');
            }

            $this->_redirect($this->view->url(['action' => 'index', 'ffid' => null]), ['prependBase' => false]);
        }

        $this->_redirect($this->view->url(['action' => 'index']), ['prependBase' => false]);
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
                $message = sprintf($this->view->translate('%d contributions linked.'), $inserted);
                $this->_flashMessenger->addMessage($message, 'success');
            }
            if (!empty($postData['link_followups'])) {
                $inserted = $followupRefModel->insertBulk(
                    !empty($postData['followup_links']) ? $postData['followup_links'] : [],
                    $snippetId,
                    'ffid'
                );
                $message = sprintf($this->view->translate('%d follow-ups linked.'), $inserted);
                $this->_flashMessenger->addMessage($message, 'success');
            }
            if (!empty($postData['link_snippets'])) {
                $inserted = $followupRefModel->insertBulk(
                    isset($postData['snippet_links']) ? $postData['snippet_links'] : [],
                    $snippetId,
                    'fid'
                );
                $message = sprintf($this->view->translate('%d snippets linked.'), $inserted);
                $this->_flashMessenger->addMessage($message, 'success');
            }
            $this->redirect($this->view->url(), ['prependBase' => false]);
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
