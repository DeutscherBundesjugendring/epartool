<?php

class FollowupController extends Zend_Controller_Action
{
    /**
     * @var Zend_Db_Table_Row_Abstract
     */
    private $consultation;

    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    private $flashMessenger;

    public function init()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $consultationModel = new Model_Consultations();
        $consultation = $consultationModel->find($kid)->current();

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        if ($consultation) {
            if (!Zend_Date::now()->isLater(new Zend_Date($consultation->vot_to, Zend_Date::ISO_8601))
                || !$consultation->is_followup_phase_showed
            ) {
                $this->flashMessenger->addMessage(
                    'For this participation round, there are no Reactions at the moment.',
                    'info'
                );
                $this->redirect('/');
            }

            $this->consultation = $consultation;
            $this->view->consultation = $consultation;
            $this->view->mediaCnsltDir = $this->view->baseUrl() . MEDIA_URL . '/consultations/' . $kid . '/';
        } else {
            $action = $this->_request->getActionName();
            if ($action != 'like' && $action != 'unlike') {
                $this->flashMessenger->addMessage('No consultation provided!', 'error');
                $this->redirect('/');
            }
        }
    }

    public function indexAction()
    {
        $kid = $this->_getParam('kid', 0);
        $followups = (new Model_FollowupFiles())->getByKid($kid, 'when DESC');
        $sbsForm = (new Service_Notification_SubscriptionFormFactory())->getForm(
            new Service_Notification_FollowUpCreatedNotification(),
            [Service_Notification_FollowUpCreatedNotification::PARAM_CONSULTATION_ID => $kid]
        );

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $auth = Zend_Auth::getInstance();
            if (isset($post['subscribe'])) {
                $this->handleSubscribeFollowUps($post, $kid, $auth, $sbsForm);
            } elseif (isset($post['unsubscribe']) && $auth->hasIdentity()) {
                $this->handleUnSubscribeFollowUps($post, $kid, $auth, $sbsForm);
            }
        }

        foreach ($followups as &$followup) {
            if (mb_strpos($followup['ref_doc'], 'http://') === 0 || mb_strpos($followup['ref_doc'], 'https://') === 0) {
                $followup['referenceType'] = 'http';
            } else {
                $followup['referenceType'] = 'file';
            }
        }
        $this->view->followups = $followups;
        $this->view->subscriptionForm = $sbsForm;
        $this->view->followupApiUrl = $this->view->url(
            ['module' => 'api', 'controller' => 'followup', 'action' => 'index'],
            null,
            true
        );
    }

    public function inputsByQuestionAction()
    {
        $tag = $this->_getParam('tag');
        $qid = $this->getRequest()->getParam('qid');
        $questionModel = new Model_Questions();
        if (!$qid) {
            $question = $questionModel->getByConsultation($this->consultation['kid'])->current();
            if ($question === null) {
                $this->flashMessenger->addMessage('There are no questions in this consultation.', 'info');
                $this->redirect($this->view->url([
                    'controller' => 'followup',
                    'action' => 'index',
                    'kid' => $this->consultation['kid']
                ], null, true));
            }
            $qid = $question['qi'];
        } else {
            $question = $questionModel->getById($qid);
        }
        $inputModel = new Model_Inputs();

        $paginator = Zend_Paginator::factory($inputModel->getSelectByQuestion($qid, 'i.when DESC', null, $tag, true));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $this->view->tag = !empty($tag) ? (new Model_Tags())->getById($tag) : null;
        $this->view->numberInputs = $inputModel->getCountByQuestion($qid, $tag);
        $this->view->question = $question;
        $this->view->paginator = $paginator;
    }

    public function showAction()
    {
        $qid = $this->_getParam('qid', 0);
        $tid = $this->_getParam('tid', 0);
        $foreset = $this->_getParam('foreset', 0);
        $inputsModel = new Model_Inputs();

        if (!$tid || !$qid) {
            $url = $this->view->url(['action' => 'index', 'kid' => $this->consultation['kid']], null, true);
            $this->redirect($url, ['prependBase' => false]);
        }

        if ($foreset) {
            $this->_helper->json->sendJson(['inputs' => $inputsModel->getRelatedWithVotesById($tid)]);
        }

        $followupsModel = new Model_Followups();
        $followupRefsModel = new Model_FollowupsRef();

        $input = $inputsModel->getById($tid);
        $input['relFowupCount'] = count($followupsModel->getByInput($tid));
        $input['votingRank'] = $this->getVotingRank($this->consultation['kid'], $input['qi'], $input['tid']);

        $relInputs = $inputsModel->getRelatedWithVotesById($tid);
        $inputIds = [];
        foreach ($relInputs as $relInput) {
            $inputIds[] = $relInput['tid'];
        }
        $countArr = $followupRefsModel->getFollowupCountByTids($inputIds);
        foreach ($relInputs as $key => &$relInput) {
            $relInput['relFowupCount'] = isset($countArr[$relInput['tid']]) ? $countArr[$relInput['tid']] : 0;
            $relInput['votingRank'] = $this->getVotingRank(
                /*
                 $this->consultation['kid'],
                 9.9.2016 hotfix/DBJR-933 jiri@visionapps.cz
                 Related inputs connected to other consultation are not valid, however they can exists and we do not
                 know with certainty, how they were created. This implementation with $relInput['kid'] is only temporary
                 */
                $relInput['kid'],
                $relInput['qi'],
                $relInput['tid']
            );
        }

        $relSnippets = $followupsModel->getByInput($tid);
        $ffIds = [];
        $snippetIds = [];
        foreach ($relSnippets as $snippet) {
            $snippetIds[] = $snippet['fid'];
            $ffIds[] = (int) $snippet['ffid'];
        }

        $indexedDocs = $this->getIndexedDocs($ffIds);
        $countArr = $followupRefsModel->getFollowupCountByFids($snippetIds, 'tid = 0');
        $relSnippets = $this->setSnippetData($relSnippets, $countArr, $indexedDocs);

        $embedVideoUrl = [];
        foreach (Zend_Registry::get('systemconfig')->video->url as $videoServiceName => $videoServiceConfig) {
            $embedVideoUrl[$videoServiceName] = $videoServiceConfig->embed->link;
        }

        $this->view->question = (new Model_Questions())->getById($qid);
        $this->view->input = $input;
        $this->view->relatedCount = count($relSnippets) + count($relInputs);
        $this->view->relInput = $relInputs;
        $this->view->relSnippets = $relSnippets;
        $this->view->followupApiUrl = $this->view->url(
            ['module' => 'api', 'controller' => 'followup', 'action' => 'index'],
            null,
            true
        );
        $this->view->embedVideoUrl = $embedVideoUrl;
    }

    /**
     * Shows the initial time line for follow-ups by chosen snippet
     */
    public function showBySnippetAction()
    {
        $fid = $this->_getParam('fid', 0);
        $tid = $this->_getParam('tid', 0);
        $foreset = $this->_getParam('foreset', 0);
        $inputsModel = new Model_Inputs();

        if (!$fid) {
            $url = $this->view->url(['action' => 'index', 'kid' => $this->consultation['kid']], null, true);
            $this->redirect($url, ['prependBase' => false]);
        }

        if ($foreset) {
            $this->_helper->json->sendJson(['inputs' => $inputsModel->getRelatedWithVotesById($tid)]);
        }

        $followupsModel = new Model_Followups();
        $followupRefsModel = new Model_FollowupsRef();

        $snippet = $followupsModel->getById($fid);

        $relTids = $followupRefsModel->getRelatedInputsByFid($fid);
        $fidRefResult = $followupRefsModel->getRelatedFollowupByFid($fid);

        $relFids = [];
        foreach ($fidRefResult as $value) {
            $relFids[] = (int)$value['fid_ref'];
        }

        $relToThisSnippets = $followupsModel->getByIdArray($relFids);
        $ffIds = [];
        foreach ($relToThisSnippets as $snippet) {
            $ffIds[] = (int)$snippet['ffid'];
        }
        $ffIds[] = (int)$snippet['ffid'];

        $indexedDocs = $this->getIndexedDocs($ffIds);
        $countArr = $followupRefsModel->getFollowupCountByFids([$fid], 'tid = 0');
        $relToThisSnippets = $this->setSnippetData($relToThisSnippets, $countArr, $indexedDocs);

        $snippet['expl'] = $this->view->wysiwyg($snippet['expl']);
        $snippet['relFowupCount'] = isset($countArr[$snippet['fid']]) ? (int) $countArr[$snippet['fid']] : 0;
        $snippet['type'] = $indexedDocs[(int)$snippet['ffid']]['type'];
        $snippet['gfx_who'] = $this->view->baseUrl()
            . MEDIA_URL . '/consultations/' . $this->consultation->kid
            . '/' . $indexedDocs[(int)$snippet['ffid']]['gfx_who'];

        $relToThisInputs = $inputsModel->getByIdArray($relTids);
        $countArr = $followupRefsModel->getFollowupCountByTids($relTids);
        foreach ($relToThisInputs as &$relInput) {
            $relInput['relFowupCount'] = isset($countArr[$relInput['tid']]) ? $countArr[$relInput['tid']] : 0;
            $relInput['votingRank'] = $this->getVotingRank(
                $this->consultation['kid'],
                $relInput['qi'],
                $relInput['tid']
            );
        }

        $embedVideoUrl = [];
        foreach (Zend_Registry::get('systemconfig')->video->url as $videoServiceName => $videoServiceConfig) {
            $embedVideoUrl[$videoServiceName] = $videoServiceConfig->embed->link;
        }

        $this->view->snippet = $snippet;
        $this->view->reltothis_snippets = $relToThisSnippets;
        $this->view->reltothis_inputs = $relToThisInputs;
        $this->view->followupApiUrl = $this->view->url(
            ['module' => 'api', 'controller' => 'followup', 'action' => 'index'],
            null,
            true
        );
        $this->view->embedVideoUrl = $embedVideoUrl;
    }

    public function jsonAction()
    {
        $kid = $this->_getParam('kid', 0);
        $tid = $this->_getParam('tid', 0);
        $fid = $this->_getParam('fid', 0);
        $ffid = $this->_getParam('ffid', 0);
        $data = [];

        //show follow-ups by fowup_rid.tid

        $inputsModel = new Model_Inputs();
        $followupsModel = new Model_Followups();
        $followupRefsModel = new Model_FollowupsRef();
        $followupFilesModel = new Model_FollowupFiles();

        $snippetids = [];
        $ffids = [];

        if ($tid) {
            $snippets = $inputsModel->getFollowups($tid);

            foreach ($snippets as $snippet) {
                $snippetids[] = $snippet['fid'];
                $ffids[] = (int) $snippet['ffid'];
            }

            $uniqueffids = array_unique($ffids);
            $docs = $followupFilesModel->getByIdArray($uniqueffids);
            $indexeddocs = [];
            foreach ($docs as $doc) {
                $indexeddocs[(int) $doc['ffid']] = $doc;
            }

            $countarr = $followupRefsModel->getFollowupCountByFids($snippetids, 'tid = 0');

            foreach ($snippets as &$snippet) {
                $snippet['expl'] = $this->view->wysiwyg($snippet['expl']);
                $snippet['gfx_who'] = $indexeddocs[(int) $snippet['ffid']]['gfx_who'];
                $snippet['relFowupCount'] = isset($countarr[$snippet['fid']]) ? (int) $countarr[$snippet['fid']] : 0;
            }
            $data['byinput']['snippets'] = $snippets;
            $data['mediafolder'] = $this->view->baseUrl() . MEDIA_URL . '/consultations/' . $kid . '/';
        }

        //show References by fowups.fid

        if ($fid) {
            $related = $followupsModel->getRelated($fid, 'tid = 0');

            foreach ($related['snippets'] as $snippet) {
                $snippetids[] = $snippet['fid'];
                $ffids[] = (int) $snippet['ffid'];
            }

            $uniqueffids = array_unique($ffids);
            $docs = $followupFilesModel->getByIdArray($uniqueffids);
            $indexeddocs = [];
            foreach ($docs as $doc) {
                $indexeddocs[(int) $doc['ffid']] = $doc;
            }

            $countarr = $followupRefsModel->getFollowupCountByFids($snippetids, 'tid = 0');

            foreach ($related['snippets'] as &$snippet) {
                $snippet['expl'] = $this->view->wysiwyg($snippet['expl']);
                $snippet['gfx_who'] = $indexeddocs[(int) $snippet['ffid']]['gfx_who'];
                $snippet['relFowupCount'] = isset($countarr[$snippet['fid']]) ? (int) $countarr[$snippet['fid']] : 0;
            }
            foreach ($related['followups'] as &$doc) {
                $doc['when'] = strtotime($doc['when']);
            }

            $data['refs']['snippets'] = $related['snippets'];
            $data['refs']['docs'] = $related['followups'];
            $data['mediafolder'] = $this->view->baseUrl() . MEDIA_URL . '/consultations/' . $kid . '/';
        }

        //show follow-up_fls by followup_fls.ffid

        if ($ffid > 0) {
            $data['doc'] = $followupFilesModel->getById($ffid);
            $data['doc']['when'] = strtotime($data['doc']['when']);
            foreach ($data['doc']['fowups'] as &$snippet) {
                $snippet['expl'] = $this->view->wysiwyg($snippet['expl']);
                $snippet['show_in_timeline_link'] = $this->view->url(
                    [
                        'action' => 'show-by-snippet',
                        'controller' => 'followup',
                        'kid' => $kid,
                        'fid' => $snippet['fid']
                    ],
                    null,
                    true
                );
            }
        }
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);

        $data['mediafolder'] = $this->view->baseUrl() . MEDIA_URL . '/consultations/' . $kid . '/';
        $this->_helper->json->sendJson($data);
    }

    /**
     * like a follow-up snippet
     * checks if UserAgent+IP combination has liked/unliked
     * sends json with like/unlike count after database update
     */
    public function likeAction()
    {
        $fid = $this->getRequest()->getParam('fid', 0);
        $result = (new Model_Followups())->supportById($fid, 'lkyea');
        $data = ['lkyea' => (string) $result];

        $this->_helper->json->sendJson($data);
    }

    /**
     * unlike a follow-up snippet
     * checks if UserAgent+IP combination has liked/unliked
     * sends json with like/unlike count after database update
     */
    public function unlikeAction()
    {
        $fid = $this->getRequest()->getParam('fid', 0);
        $result = (new Model_Followups())->supportById($fid, 'lknay');
        $data = ['lknay' => (string) $result];

        $this->_helper->json->sendJson($data);
    }

    public function tagsAction()
    {
        $this->view->inputCount = (new Model_Inputs())->getCountByConsultation($this->consultation->kid);
        $this->view->tags = (new Model_Tags())->getAllByConsultation($this->consultation->kid, '', true, true);
    }

    public function downloadAction()
    {
        $filename = $this->getRequest()->getParam('filename', 0);
        $uploadDir = realpath(MEDIA_PATH . '/misc');
        if ($this->consultation) {
            $uploadDir = realpath(MEDIA_PATH . '/consultations/' . $this->consultation->kid);
        }

        $file = $uploadDir . '/' . $filename;
        if (is_file($file)) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-length: " . filesize($file));
            header("Content-Disposition: attachment;filename={$filename}");
            header("Content-Description: File Transfer");
            ob_clean();
            flush();
            readfile($file);
        } else {
            $this->flashMessenger->addMessage('File does not exist.', 'error');
            $this->redirect(
                $this->view->url(['action' => 'index', 'kid' => $this->consultation->kid]),
                ['prependBase' => false]
            );
        }
    }

    /**
     * @param array $post
     * @param $kid
     * @param \Zend_Auth $auth
     * @param \Default_Form_SubscribeNotification $sbsForm
     * @throws \Exception
     * @throws \Zend_Exception
     * @throws \Zend_Form_Exception
     */
    private function handleSubscribeFollowUps(
        array $post,
        $kid,
        Zend_Auth $auth,
        Default_Form_SubscribeNotification $sbsForm
    ) {
        if ($auth->hasIdentity()) {
            Zend_Registry::get('dbAdapter')->beginTransaction();
            try {
                $userId = $auth->getIdentity()->uid;
                (new Service_Notification_FollowUpCreatedNotification())->subscribeUser(
                    $userId,
                    [Service_Notification_FollowUpCreatedNotification::PARAM_CONSULTATION_ID => $kid],
                    Service_Notification_FollowUpCreatedNotification::POSTSUBSCRIBE_ACTION_CONFIRM_IMMEDIATE
                );
                Zend_Registry::get('dbAdapter')->commit();
                $this->flashMessenger->addMessage('Thank you for subscribing.', 'success');
                $this->redirect('/followup/index/kid/' . $kid);
            } catch (Exception $e) {
                Zend_Registry::get('dbAdapter')->rollback();
                throw $e;
            }
        } else {
            if (isset($post['email'])) {
                if ($sbsForm->isValid($post)) {
                    Zend_Registry::get('dbAdapter')->beginTransaction();
                    try {
                        list($userId) = (new Model_Users())->register($sbsForm->getValues());
                        (new Service_Notification_FollowUpCreatedNotification())->subscribeUser(
                            $userId,
                            [Service_Notification_FollowUpCreatedNotification::PARAM_CONSULTATION_ID => $kid],
                            Service_Notification_FollowUpCreatedNotification::POSTSUBSCRIBE_ACTION_CONFIRM_EMAIL_REQUEST
                        );
                        Zend_Registry::get('dbAdapter')->commit();
                        $this->flashMessenger->addMessage(
                            'You are now subscribed. A confirmation email has been sent.',
                            'success'
                        );
                        $this->redirect('/followup/index/kid/' . $kid);
                    } catch (Dbjr_Notification_Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        $this->flashMessenger->addMessage('You are already subscribed.', 'success');
                        $this->redirect('/followup/index/kid/' . $kid);
                    } catch (Exception $e) {
                        Zend_Registry::get('dbAdapter')->rollback();
                        throw $e;
                    }
                } else {
                    $this->flashMessenger->addMessage('The subscription form is invalid.', 'error');
                }
            }
        }
    }

    /**
     * Handles request to unsubscribe user from recieving notifications of new inputs belonging to this question
     * @param array $post The data received in post request
     * @param int $kid The consultation identifier
     * @param \Zend_Auth $auth The auth adapter
     * @param \Default_Form_UnsubscribeNotification $unSbsForm
     */
    private function handleUnSubscribeFollowUps(
        array $post,
        $kid,
        Zend_Auth $auth,
        Default_Form_UnsubscribeNotification $unSbsForm
    ) {
        if ($unSbsForm->isValid($post)) {
            $userId = $auth->getIdentity()->uid;
            (new Service_Notification_FollowUpCreatedNotification())->unsubscribeUser(
                $userId,
                [Service_Notification_FollowUpCreatedNotification::PARAM_CONSULTATION_ID => $kid]
            );
            $this->flashMessenger->addMessage('You have been unsubscribed.', 'success');
            $this->redirect('/followup/index/kid/' . $kid);
        }
    }

    /**
     * @param int $kid
     * @param int $qid
     * @param int $inputId
     * @return int
     */
    private function getVotingRank($kid, $qid, $inputId)
    {
        $results = (new Model_Votes())->getResultsValues($kid, $qid);
        foreach ($results['votings'] as $i => $result) {
            if ($result['tid'] === $inputId) {
                return $i + 1;
            }
        }
    }

    /**
     * @param array $snippets
     * @param array $countArr
     * @param array $indexedDocs
     * @return array
     */
    private function setSnippetData(array $snippets, array $countArr, array $indexedDocs)
    {
        foreach ($snippets as &$snippet) {
            $snippet['expl'] = $this->view->wysiwyg($snippet['expl']);
            $snippet['relFowupCount'] = isset($countArr[$snippet['fid']]) ? (int)$countArr[$snippet['fid']] : 0;
            $snippet['gfx_who'] = $this->view->baseUrl()
                . MEDIA_URL . '/consultations/' . $this->consultation['kid']
                . '/' . $indexedDocs[(int)$snippet['ffid']]['gfx_who'];
        }

        return $snippets;
    }

    /**
     * @param array $ffIds
     * @return array
     */
    private function getIndexedDocs(array $ffIds)
    {
        $indexedDocs = [];
        $docs = (new Model_FollowupFiles())->getByIdArray(array_unique($ffIds));
        foreach ($docs as $doc) {
            $indexedDocs[(int)$doc['ffid']] = $doc;
        }

        return $indexedDocs;
    }
}
