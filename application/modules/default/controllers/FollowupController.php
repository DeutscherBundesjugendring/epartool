<?php

/**
 *
 * @author Marco Dinnbier
 */
class FollowupController extends Zend_Controller_Action
{
    protected $_consultation = null;

    /**
     * Construct
     * @see Zend_Controller_Action::init()
     * @return void
     */
    public function init()
    {
        $kid = $this->getRequest()->getParam('kid', 0);
        $consultationModel = new Model_Consultations();
        $consultation = $consultationModel->find($kid)->current();

        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');

        if ($consultation) {

            $nowDate = Zend_Date::now();

            if (
                !$nowDate->isLater($consultation->vot_to)
                || $consultation->follup_show == 'n'
            ) {
                $this->_flashMessenger->addMessage(
                    'For this participation round, there are no Reactions at the moment.',
                    'info'
                );
                $this->redirect('/');
            }

            $this->_consultation = $consultation;
            $this->view->consultation = $consultation;
            $this->view->mediaCnsltDir = $this->view->baseUrl() . '/media/consultations/' . $kid . '/';
        } else {
            $action = $this->_request->getActionName();
            if ($action != 'like' && $action != 'unlike') {
                $this->_flashMessenger->addMessage('No consultation provided!', 'error');
                $this->_redirect('/');
            }
        }
    }

    /**
     * Followups Page: Index
     * assigns the latest FollowupDocs by kid from fowup_fls
     *
     * @name index
     * @param $_GET['kid'] consultation id
     * @return void
     */
    public function indexAction()
    {
        $kid = $this->_getParam('kid', 0);
        $followupModel = new Model_FollowupFiles();
        $followups = $followupModel->getByKid($kid, 'when DESC');
        foreach ($followups as &$followup) {
            if (
                strpos($followup['ref_doc'], 'http://') === 0
                || strpos($followup['ref_doc'], 'https://') === 0
            ) {
                $followup['referenceType'] = 'http';
            } else {
                $followup['referenceType'] = 'file';
            }
        }
        $this->view->followups = $followups;
    }

    /**
     * Followups Page: Inputs by Questions
     * assigns the data for questions and inputs
     *
     * @param $_GET['qid'] question id
     * @param $_GET['kid'] consultation id
     *
     * @return void
     */
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

        $paginator = Zend_Paginator::factory($inputModel->getSelectByQuestion($qid, 'i.uid ASC', null, $tag));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;
    }

    /*
     * shows the initial timeline for followups by chosen input
     *
     * @param $_GET['kid'] consultation id
     * @param $_GET['qid'] question id
     * @param $_GET['tid'] input id
     *
     * @return void
     */

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
                $inputids = array();

                foreach ($relInputs as $relInput) {
                    $inputids[] = $relInput['tid'];
                }

                $countarr = $followupRefsModel->getFollowupCountByTids($inputids);

                foreach ($relInputs as $key => $relInput) {
                    $relInputs[$key]['relFowupCount'] = isset($countarr[$relInput['tid']]) ? $countarr[$relInput['tid']] : 0;
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
                $indexeddocs = array();
                foreach ($docs as $doc) {
                    $indexeddocs[(int) $doc['ffid']] = $doc;
                }

                $countarr = $followupRefsModel->getFollowupCountByFids($snippetids, 'tid = 0');

                foreach ($relSnippets as &$snippet) {
                    $snippet['expl'] = html_entity_decode($snippet['expl']);
                    $snippet['gfx_who'] = $this->view->baseUrl()
                        . '/media/consultations/' . $kid
                        . '/'.$indexeddocs[(int) $snippet['ffid']]['gfx_who'];
                    $snippet['relFowupCount'] = isset($countarr[$snippet['fid']]) ? (int) $countarr[$snippet['fid']] : 0;
                }

                $relatedCount = count($relSnippets) + count($relInputs);

                // result via json for followoptool

                $this->view->assign(
                    array(
                        'question' => $question,
                        'input' => $input,
                        'relatedCount' => $relatedCount,
                        'relInput' => $relInputs,
                        'relSnippets' => $relSnippets,
                        'kid' => $kid
                    )
                );
            }
        } else {
            if ($kid) {
                $this->_redirect(
                    $this->view->url(array('action' => 'index', 'kid' => $kid), null, true)
                );
            } else {
                $this->_redirect('/');
            }
        }
    }
    /*
     * shows the initial timeline for followups by chosen snippet
     *
     * @param $_GET['kid'] consultation id
     * @param $_GET['qid'] question id
     * @param $_GET['fid'] followup id
     *
     * @return void
     */

    public function showBySnippetAction()
    {
        $kid = $this->_getParam('kid', 0);
        $fid = $this->_getParam('fid', 0);

        $foreset = $this->_getParam('foreset', 0);

        if ($kid && $fid) {

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

                $relFids = array();
                foreach ($fidRefResult as $value) {
                    $relFids[] = (int) $value['fid_ref'];
                }

                $reltothisInputs = $inputsModel->getByIdArray($relTids);
                $reltothisSnippets = $followupsModel->getByIdArray($relFids);

                $snippetids = array();
                $ffids = array();

                foreach ($reltothisSnippets as $snippet) {
                    $snippetids[] = $snippet['fid'];
                    $ffids[] = (int) $snippet['ffid'];
                }

                $ffids[] = (int) $currentSnippet["ffid"];

                $uniqueffids = array_unique($ffids);

                $docs = $followupFilesModel->getByIdArray($uniqueffids);

                $indexeddocs = array();

                foreach ($docs as $doc) {
                    $indexeddocs[(int) $doc['ffid']] = $doc;
                }
                $fidsToCount = $relFids;
                $fidsToCount[] = $fid;

                $countarrSnippets = $followupRefsModel->getFollowupCountByFids($fidsToCount, 'tid = 0');

                foreach ($reltothisSnippets as &$snippet) {
                    $snippet['expl'] = html_entity_decode($snippet['expl']);
                    $snippet['gfx_who'] = $this->view->baseUrl()
                        . '/media/consultations/' . $kid
                        . '/'.$indexeddocs[(int) $snippet['ffid']]['gfx_who'];
                    $snippet['relFowupCount'] = isset($countarrSnippets[$snippet['fid']]) ? (int) $countarrSnippets[$snippet['fid']] : 0;
                }

                $currentSnippet['expl'] = html_entity_decode($currentSnippet['expl']);
                $currentSnippet['gfx_who'] = $this->view->baseUrl()
                    . '/media/consultations/' . $kid
                    . '/'.$indexeddocs[(int) $currentSnippet['ffid']]['gfx_who'];
                $currentSnippet['relFowupCount'] = isset($countarrSnippets[$currentSnippet['fid']]) ? (int) $countarrSnippets[$currentSnippet['fid']] : 0;

                $countarrInputs = $followupRefsModel->getFollowupCountByTids($relTids);

                foreach ($reltothisInputs as &$relInput) {
                    $relInput['relFowupCount'] = isset($countarrInputs[$relInput['tid']]) ? $countarrInputs[$relInput['tid']] : 0;
                }

                $this->view->assign(
                    array(
                        'snippet' => $currentSnippet,
                        'reltothis_snippets' => $reltothisSnippets,
                        'reltothis_inputs' => $reltothisInputs,
                        'kid' => $kid
                    )
                );

            }
        } else {

            if ($kid) {

                $this->_redirect(
                    $this->view->url(array('action' => 'index', 'kid' => $kid), null, true)
                );
            } else {
                $this->_redirect('/');
            }
        }
    }

    /*
     *
     * sends jsondata
     *
     * @param $_GET['kid'] consultation id
     * @param $_GET['tid'] show followups by fowup_rid.tid
     * @param $_GET['fid'] show References by fowups.fid
     * @param $_GET['ffid'] show followup_fls by followup_fls.ffid
     * @return void
     *
     */

    public function jsonAction()
    {
        $kid = $this->_getParam('kid', 0);
        $tid = $this->_getParam('tid', 0);
        $fid = $this->_getParam('fid', 0);
        $ffid = $this->_getParam('ffid', 0);
        $data = array();

        //show followups by fowup_rid.tid

        $inputsModel = new Model_Inputs();
        $followupsModel = new Model_Followups();
        $followupRefsModel = new Model_FollowupsRef();
        $followupFilesModel = new Model_FollowupFiles();

        $snippetids = array();
        $ffids = array();

        if ($tid) {

            $snippets = $inputsModel->getFollowups($tid);

            foreach ($snippets as $snippet) {
                $snippetids[] = $snippet['fid'];
                $ffids[] = (int) $snippet['ffid'];
            }

            $uniqueffids = array_unique($ffids);
            $docs = $followupFilesModel->getByIdArray($uniqueffids);
            $indexeddocs = array();
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
            $indexeddocs = array();
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

        //show followup_fls by followup_fls.ffid
        if ($ffid > 0) {

            $data['doc'] = $followupFilesModel->getById($ffid);
            $data['doc']['when'] = strtotime($data['doc']['when']);
            foreach ($data['doc']['fowups'] as &$snippet) {

                $snippet['expl'] = html_entity_decode($snippet['expl']);
                $snippet['show_in_timeline_link'] = $this->view->url(
                    array(
                        'action' => 'show-by-snippet',
                        'controller' => 'followup',
                        'kid' => $kid,
                        'fid' => $snippet['fid']
                    ),
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

    /*
     * like a followup-snippet
     * checks if UserAgent+IP combination has liked/unliked
     * sends json with like/unlike count after database update
     *
     * @param $_GET['fid'] fowup.fid
     *
     * @return void
     */

    public function likeAction()
    {
        $fid = $this->getRequest()->getParam('fid', 0);

        $followups = new Model_Followups();
        $result = $followups->supportById($fid, 'lkyea');

        $data = array('lkyea' => (string) $result);

        $this->_helper->json->sendJson($data);
    }

    /*
     * unlike a followup-snippet
     * checks if UserAgent+IP combination has liked/unliked
     * sends json with like/unlike count after database update
     *
     * @param $_GET['fid'] fowup.fid
     *
     * @return void
     */

    public function unlikeAction()
    {
        $fid = $this->getRequest()->getParam('fid', 0);

        $followups = new Model_Followups();
        $result = $followups->supportById($fid, 'lknay');
        $data = array('lknay' => (string) $result);

        $this->_helper->json->sendJson($data);
    }

    public function tagsAction()
    {
        $kid = $this->_request->getParam('kid', 0);
        $inputModel = new Model_Inputs();
        $tagModel = new Model_Tags();

        $this->view->inputCount = $inputModel->getCountByConsultation($this->_consultation->kid);

        $this->view->tags = $tagModel->getAllByConsultation($kid);
    }

    public function downloadAction()
    {
        $filename = $this->getRequest()->getParam('filename', 0);
        $kid = $this->getRequest()->getParam('kid', 0);
        $mediaPath = Zend_Registry::get('systemconfig')->media->path;

        if ($kid) {
            $uploadDir = realpath(MEDIA_PATH . '/consultations/' . $kid);
        } else {
            $uploadDir = realpath(MEDIA_PATH . '/misc');
        }

        $file = $uploadDir . '/' . $filename;
        if (is_file($file)) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
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
            $this->_flashMessenger->addMessage('File does not exist.', 'error');
            $this->redirect(
                $this->view->url(array('action' => 'index', 'kid' => $kid)),
                array('prependBase' => false)
            );
        }
    }

}
