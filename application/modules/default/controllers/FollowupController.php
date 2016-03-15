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
                || $consultation->follup_show == 'n'
            ) {
                $this->flashMessenger->addMessage(
                    'For this participation round, there are no Reactions at the moment.',
                    'info'
                );
                $this->redirect('/');
            }

            $this->consultation = $consultation;
            $this->view->consultation = $consultation;
            $this->view->mediaCnsltDir = $this->view->baseUrl() . '/media/consultations/' . $kid . '/';
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
        $followupModel = new Model_FollowupFiles();
        $followups = $followupModel->getByKid($kid, 'when DESC');
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
                $this->handleUnsubscribeFollowUps($post, $kid, $auth, $sbsForm);
            }
        }

        foreach ($followups as &$followup) {
            if (strpos($followup['ref_doc'], 'http://') === 0
                || strpos($followup['ref_doc'], 'https://') === 0
            ) {
                $followup['referenceType'] = 'http';
            } else {
                $followup['referenceType'] = 'file';
            }
        }
        $this->view->followups = $followups;
        $this->view->subscriptionForm = $sbsForm;
    }

    public function inputsByQuestionAction()
    {
        $kid = $this->_getParam('kid', 0);
        $qid = $this->getRequest()->getParam('qid', 0);
        $tag = $this->_getParam('tag', null);

        $inputModel = new Model_Inputs();
        $questionModel = new Model_Questions();

        if (!empty($tag)) {
            $tagModel = new Model_Tags();
            $this->view->tag = $tagModel->getById($tag);
        }

        if (empty($qid)) {
            // get first question of this consultation
            $questionRow = $questionModel->getByConsultation($kid)->current();
            $qid = $questionRow->qi;
        }

        $this->view->numberInputs = $inputModel->getCountByQuestion($qid, $tag);
        $this->view->question = $questionModel->getById($qid);

        $paginator = Zend_Paginator::factory($inputModel->getSelectByQuestion($qid, 'i.when DESC', null, $tag));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;
    }

    public function showAction()
    {
        $kid = $this->_getParam('kid', 0);
        $qid = $this->_getParam('qid', 0);
        $tid = $this->_getParam('tid', 0);
        $foreset = $this->_getParam('foreset', 0);

        if ($kid && $tid && $qid) {
            if ($foreset) {
                $inputsModel = new Model_Inputs();
                $relInputs = $inputsModel->getRelatedWithVotesById($tid);

                $data['inputs'] = $relInputs;
                $this->_helper->json->sendJson($data);
            } else {
                $inputsModel = new Model_Inputs();
                $questionsModel = new Model_Questions();
                $followupsModel = new Model_Followups();
                $followupRefsModel = new Model_FollowupsRef();
                $followupFilesModel = new Model_FollowupFiles();

                $question = $questionsModel->getById($qid);

                $input = $inputsModel->getById($tid);
                $input['relFowupCount'] = count($followupsModel->getByInput($tid));

                $relInputs = $inputsModel->fetchAll(
                    $inputsModel
                        ->select()
                        ->where('tid IN (?)', explode(',', $input['rel_tid']))
                )->toArray();
                $inputids = [];

                foreach ($relInputs as $relInput) {
                    $inputids[] = $relInput['tid'];
                }

                $countarr = $followupRefsModel->getFollowupCountByTids($inputids);
                foreach ($relInputs as $key => $relInput) {
                    $relInputs[$key]['relFowupCount'] = isset($countarr[$relInput['tid']])
                        ? $countarr[$relInput['tid']]
                        : 0;
                }

                $relSnippets = $followupsModel->getByInput($tid);

                $ffids = [];
                $snippetids = [];
                foreach ($relSnippets as $snippet) {
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

                foreach ($relSnippets as &$snippet) {
                    $snippet['expl'] = html_entity_decode($snippet['expl']);
                    $snippet['gfx_who'] = $this->view->baseUrl()
                        . '/media/consultations/' . $kid
                        . '/'.$indexeddocs[(int) $snippet['ffid']]['gfx_who'];
                    $snippet['relFowupCount'] = isset($countarr[$snippet['fid']])
                        ? (int) $countarr[$snippet['fid']]
                        : 0;
                }

                $relatedCount = count($relSnippets) + count($relInputs);

                // result via json for followop
                $this->view->assign([
                    'question' => $question,
                    'input' => $input,
                    'relatedCount' => $relatedCount,
                    'relInput' => $relInputs,
                    'relSnippets' => $relSnippets,
                    'kid' => $kid,
                    'hasFollowupTimeline' => true,
                ]);
            }
        } else {
            if ($kid) {
                $this->redirect(
                    $this->view->url(['action' => 'index', 'kid' => $kid], null, true),
                    ['prependBase' => false]
                );
            } else {
                $this->redirect('/');
            }
        }
    }
    /**
     * Shows the initial time line for follow-ups by chosen snippet
     */
    public function showBySnippetAction()
    {
        $fid = $this->_getParam('fid', 0);
        $tid = $this->_getParam('tid', 0);
        $foreset = $this->_getParam('foreset', 0);

        if ($this->consultation && $fid) {
            if ($foreset) {
                $inputsModel = new Model_Inputs();
                $relInputs = $inputsModel->getRelatedWithVotesById($tid);

                $data['inputs'] = $relInputs;
                $this->_helper->json->sendJson($data);
            } else {
                $inputsModel = new Model_Inputs();
                $followupsModel = new Model_Followups();
                $followupRefsModel = new Model_FollowupsRef();
                $followupFilesModel = new Model_FollowupFiles();

                $currentSnippet = $followupsModel->getById($fid);

                $relTids = $followupRefsModel->getRelatedInputsByFid($fid);
                $fidRefResult = $followupRefsModel->getRelatedFollowupByFid($fid);

                $relFids = [];
                foreach ($fidRefResult as $value) {
                    $relFids[] = (int) $value['fid_ref'];
                }

                $reltothisInputs = $inputsModel->getByIdArray($relTids);
                $reltothisSnippets = $followupsModel->getByIdArray($relFids);

                $snippetids = [];
                $ffids = [];

                foreach ($reltothisSnippets as $snippet) {
                    $snippetids[] = $snippet['fid'];
                    $ffids[] = (int) $snippet['ffid'];
                }

                $ffids[] = (int) $currentSnippet['ffid'];
                $uniqueffids = array_unique($ffids);
                $docs = $followupFilesModel->getByIdArray($uniqueffids);
                $indexeddocs = [];

                foreach ($docs as $doc) {
                    $indexeddocs[(int) $doc['ffid']] = $doc;
                }
                $fidsToCount = [$fid];
                $countarrSnippets = $followupRefsModel->getFollowupCountByFids($fidsToCount, 'tid = 0');

                foreach ($reltothisSnippets as &$snippet) {
                    $snippet['expl'] = html_entity_decode($snippet['expl']);
                    $snippet['gfx_who'] = $this->view->baseUrl()
                        . '/media/consultations/' . $this->consultation->kid
                        . '/'.$indexeddocs[(int) $snippet['ffid']]['gfx_who'];
                    $snippet['relFowupCount'] = isset($countarrSnippets[$snippet['fid']])
                        ? (int) $countarrSnippets[$snippet['fid']]
                        : 0;
                }

                $currentSnippet['expl'] = html_entity_decode($currentSnippet['expl']);
                $currentSnippet['gfx_who'] = $this->view->baseUrl()
                    . '/media/consultations/' . $this->consultation->kid
                    . '/'.$indexeddocs[(int) $currentSnippet['ffid']]['gfx_who'];
                $currentSnippet['relFowupCount'] = isset($countarrSnippets[$currentSnippet['fid']])
                    ? (int) $countarrSnippets[$currentSnippet['fid']]
                    : 0;

                $countarrInputs = $followupRefsModel->getFollowupCountByTids($relTids);

                foreach ($reltothisInputs as &$relInput) {
                    $relInput['relFowupCount'] = isset($countarrInputs[$relInput['tid']])
                        ? $countarrInputs[$relInput['tid']]
                        : 0;
                }

                $this->view->assign([
                    'snippet' => $currentSnippet,
                    'reltothis_snippets' => $reltothisSnippets,
                    'reltothis_inputs' => $reltothisInputs,
                    'kid' => $this->consultation->kid,
                    'hasFollowupTimeline' => true,
                ]);
            }
        } else {
            if ($this->consultation) {
                $this->redirect(
                    $this->view->url(['action' => 'index', 'kid' => $this->consultation->kid], null, true),
                    ['prependBase' => false]
                );
            } else {
                $this->redirect('/');
            }
        }
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
                $snippet['expl'] = html_entity_decode($snippet['expl']);
                $snippet['gfx_who'] = $indexeddocs[(int) $snippet['ffid']]['gfx_who'];
                $snippet['relFowupCount'] = isset($countarr[$snippet['fid']]) ? (int) $countarr[$snippet['fid']] : 0;
            }
            $data['byinput']['snippets'] = $snippets;
            $data['mediafolder'] = $this->view->baseUrl() . '/media/consultations/' . $kid . '/';
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
                $snippet['expl'] = html_entity_decode($snippet['expl']);
                $snippet['gfx_who'] = $indexeddocs[(int) $snippet['ffid']]['gfx_who'];
                $snippet['relFowupCount'] = isset($countarr[$snippet['fid']]) ? (int) $countarr[$snippet['fid']] : 0;
            }
            foreach ($related['followups'] as &$doc) {
                $doc['when'] = strtotime($doc['when']);
            }

            $data['refs']['snippets'] = $related['snippets'];
            $data['refs']['docs'] = $related['followups'];
            $data['mediafolder'] = $this->view->baseUrl() . '/media/consultations/' . $kid . '/';
        }

        //show follow-up_fls by followup_fls.ffid

        if ($ffid > 0) {
            $data['doc'] = $followupFilesModel->getById($ffid);
            $data['doc']['when'] = strtotime($data['doc']['when']);
            foreach ($data['doc']['fowups'] as &$snippet) {
                $snippet['expl'] = html_entity_decode($snippet['expl']);
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

        $data['mediafolder'] = $this->view->baseUrl() . '/media/consultations/' . $kid . '/';
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
        $this->view->tags = (new Model_Tags())->getAllByConsultation($this->consultation->kid);
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
    private function handleUnsubscribeFollowUps(
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

}
