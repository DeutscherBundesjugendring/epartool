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
            $params = $this->getRequest()->getPost();
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
                $newId = $followupFilesRow->save();

                $this->_flashMessenger->addMessage('The followup was created successfully.', 'success');
                $this->_redirect($this->view->url(['action' => 'edit-file', 'ffid' => $newId]));
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

        $snippetModel = new Model_Followups();
        $followupRefModel = new Model_FollowupsRef();
        $followupModel = new Model_FollowupFiles();

        $snippets = array();
        $result = $followupModel->getFollowupsById($ffid, 'docorg ASC')->toArray();
        foreach ($result as $followup) {
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





    function showRelatedSnippetsAction()
    {
        $this->_helper->layout->setLayout('popup');
        $fid = $this->getRequest()->getParam('fid', 0);
        if ($fid) {
            $fidarr = array();
            $followupRefModel = new Model_FollowupsRef();
            $followupsModel = new Model_Followups();
            $followupFilesModel = new Model_FollowupFiles();

            $reltothis = $followupRefModel->getRelatedFollowupByFid($fid);
            foreach ($reltothis as $value) {
                array_push($fidarr, (int) $value['fid_ref']);
            }
            $snippets = $followupsModel->getByIdArray($fidarr);
            $snippetsgrouped = array();
            foreach ($snippets as $snippet) {
                $snippetsgrouped[$snippet['ffid']][] = $snippet;
            }

            foreach ($snippetsgrouped as $ffid => $snippets) {
                $snippetsgrouped[$ffid]['doc'] = $followupFilesModel->getById($ffid, true);
            }
            $snippet = $followupsModel->getById($fid);
            $this->view->assign(
                array(
                    'snippet' => $snippet,
                    'relsnippets' => $snippetsgrouped
                )
            );
        }
    }

    /*
     * Move snippet in fowups
     * @see editFileAction
     * @param $_GET['kid'] integer consultation id
     * @param $_GET['ffid'] integer fowup_fls.ffid
     * @param $_GET['fid'] integer fowups.fid
     * @param $_GET['movefid'] integer fowups.fid of moved snippet
     * @param $_GET['prev'] integer move after prev (docorg of previous snippet)
     */
    public function moveAction()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $fid = $this->getRequest()->getParam('fid', 0);
        $ffid = $this->getRequest()->getParam('ffid', 0);
        $movefid = $this->getRequest()->getParam('movefid', 0);
        $moveto = $this->getRequest()->getParam('moveto', 0);
        $prev = $this->getRequest()->getParam('prev');

        if ($moveto) {
            $followupFiles = new Model_FollowupFiles();
            $followupsModel = new Model_Followups();
            $followupsByFile = $followupFiles->getFollowupsById($ffid, 'docorg ASC');
            $movedfollowup = $followupsModel->find($fid)->current();

            foreach ($followupsByFile as $followup) {
                if ($followup->docorg === $moveto) {
                    $followup->docorg = $movedfollowup->docorg;
                    $movedfollowup->docorg = $moveto;
                    $movedfollowup->save();
                    $followup->save();
                }
            }
        }

        $this->_forward('edit-file');
    }

    /*
     * increment/decrement hierarchy level for snippets in fowups
     * @param $_GET['kid'] integer consultation id
     * @param $_GET['ffid'] integer fowup_fls.ffid
     * @param $_GET['fid'] integer fowups.fid
     * @param $_GET['hlvl'] integer fowups.hlvl
     */
    public function hierarchyAction()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $fid = $this->getRequest()->getParam('fid', 0);
        $ffid = $this->getRequest()->getParam('ffid', 0);
        $hlvl = $this->getRequest()->getParam('hlvl', 0);

        if ($hlvl > 0 && $hlvl < 7 && $fid > 0) {
            $followupsModel = new Model_Followups();
            $followupsRow = $followupsModel->find($fid)->current();
            $followupsRow->hlvl = $hlvl;
         $followupsRow->save();
        }
        $this->_redirect(
            $this->view->url(
                array(
                    'action' => 'edit-file',
                    'kid' => $this->_kid,
                    'ffid' => $ffid
                )
            ),
            array('prependBase' => false)
        );
    }

    public function referenceAction()
    {
        $this->_helper->layout->setLayout('popup');
        $followupsModel = new Model_Followups();
        $inputsModel = new Model_Inputs();
        $followupFilesModel = new Model_FollowupFiles();
        $questionsModel = new Model_Questions();

        $kid = $this->getRequest()->getParam('kid', 0);
        $fid = $this->getRequest()->getParam('fid', 0);

        $docs = array();
        $snippets = array();

        if ($kid > 0) {
            $ffidArray = array();
            $docs = $followupFilesModel->getByKid($kid, 'when DESC');
            foreach ($docs as $doc) {
                $ffidArray[] = $doc['ffid'];
            }
            $snippets = $followupsModel->getByDocIdArray($ffidArray);
        }

        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            if (!empty($params['question'])) {
                $question = $questionsModel->getById($params['question']);
            }
            if (!empty($params['chosenDoc'])) {
                $chosenDoc = $params['chosenDoc'];
            }

            $followupRefModel = new Model_FollowupsRef();

            if (!empty($params['inp_list']) && !empty($params['insert_inputs'])) {
                $inserted = $followupRefModel->insertBulk($params['inp_list'], $fid, 'tid');
                $message = "$inserted Beiträge wurden zugeordnet.";
                $this->_flashMessenger->addMessage($message, 'success');
            }
            if (!empty($params['doc_list']) && !empty($params['insert_docs'])) {
                $inserted = $followupRefModel->insertBulk($params['doc_list'], $fid, 'ffid');
                $message = "$inserted Dokumente wurden zugeordnet.";
                $this->_flashMessenger->addMessage($message, 'success');
            }
            if (!empty($params['snippet_list']) && !empty($params['insert_snippets'])) {
                $inserted = $followupRefModel->insertBulk($params['snippet_list'], $fid, 'fid');
                $message = "$inserted Snippets wurden zugeordnet.";
                $this->_flashMessenger->addMessage($message, 'success');
            }

        }

        $related = $followupsModel->getRelated($fid);
        $followup = $followupsModel->getById($fid);

        if (empty($question)) {
            // get first question of this consultation
            $questionRow = $questionsModel->getByConsultation($kid)->current();
            $question = $questionsModel->getById($questionRow->qi);
        }
        if (empty($chosenDoc)) {
            // get first question of this consultation
            $followupFile = $followupFilesModel->getByKid($kid, 'when DESC', NULL, $followup['ffid']);
            if ($followupFile) {
                $chosenDoc = $followupFile[0]['ffid'];
            }
        }
        $this->view->assign(
            array(
                'kid' => $kid,
                'followup' => $followup,
                'related' => $related,
                'snippets' => $snippets,
                'docs' => $docs,
                'question' => $question,
                'chosenDoc' => isset($chosenDoc) ? $chosenDoc : null,
            )
        );
    }

    public function delReferenceAction()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $fidRef = $this->getRequest()->getParam('fid_ref', 0);
        $fid = $this->getRequest()->getParam('fid', 0);
        $tid = $this->getRequest()->getParam('tid', 0);
        $ffid = $this->getRequest()->getParam('ffid', 0);

        if ($fid > 0) {
            $reftype = 'fid';
            $refkey = $fid;
        }
        if ($tid > 0) {
            $reftype = 'tid';
            $refkey = $tid;
        }
        if ($ffid > 0) {
            $reftype = 'ffid';
            $refkey = $ffid;
        }

        $followupRefModel = new Model_FollowupsRef();
        $followupRefModel->deleteRef($fidRef, $reftype, $refkey);
        $this->_redirect(
            $this->view->url(
                array(
                    'module' => 'admin',
                    'controller' => 'followup',
                    'action' => 'reference',
                    'kid' => $kid,
                    'fid' => $fidRef
                ),
                null,
                true
            )
        );
    }
}
