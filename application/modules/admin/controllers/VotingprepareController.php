<?php

class Admin_VotingprepareController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_consultation = null;

    /**
     * The current question id
     * @var integer
     */
    protected $_qid;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_params = $this->_request->getParams();
        $this->_consultation = $this->getKid($this->_params);
        $this->_qid = $this->getQid($this->_params);
        if (isset($this->_params["tid"])) {
            $this->_tid = $this->getTId($this->_params);
        }
    }

    /**
     * Shows the list of questions
     */
    public function indexAction()
    {
        $this->view->consultation = $this->_consultation;
    }

    /**
     * Returns parameters for list of inputs, questions and directories
     */
    public function overviewAction()
    {
        $form = new Admin_Form_ListControl();

        $wheres['inpt.qi = ?'] = $this->_qid;
        $fulltext = $this->getRequest()->getParam('fulltext', null);
        if ($fulltext) {
            $wheres['thes LIKE ?'] = '%' . $fulltext . '%';
        }

        $this->view->inputs = (new Model_Inputs())->fetchAllInputs($wheres);
        $this->view->fulltext = $fulltext;
        $this->view->consultation = $this->_consultation;
        $this->view->directories = (new Model_Directories())->getByQuestion($this->_qid);
        $this->view->tags = (new Model_Tags())->getAll()->toArray();
        $this->view->form = $form;
    }

    /**
     * Updates the directory for given inputs and redirect to overviewAction
     */
    public function setdirectoryAction()
    {
        $this->_helper->layout()->disableLayout();

        if (!empty($this->_params['thesis'])) {

            $this->checkInputIDs($this->_params['thesis']);

            $options = array();
            $options['dir'] = $this->getDirId($this->_params);
            $options['thesis'] = implode(",", $this->_params['thesis']);

            $inputsModel = new Model_Inputs();
            $inputsModel->setDirectory($options);
            $this->_flashMessenger->addMessage('Die markierten Beiträge wurden verschoben', 'success');
        } else {
            $this->_flashMessenger->addMessage('Es wurden keine Beiträge ausgewählt', 'error');
        }
        $this->redirect(
            '/admin/votingprepare/overview/kid/'
            . $this->_consultation["kid"]
            . '/qid/'
            . $this->_qid
            . '/dir/'
            . $this->getDirId($this->_params)
        );
    }

    /**
     * Makes changes to Inputs from the input list contect in bulk and individualy
     */
    public function listControlAction()
    {
        if ($this->getRequest()->isPost()
            && (new Admin_Form_ListControl())->isValid($this->getRequest()->getPost())
        ) {
            $inputModel = new Model_Inputs();
            $inputIds = $this->getRequest()->getPost('inputIds');
            if ($inputIds) {
                if ($this->getRequest()->getPost('releaseBulk', null)) {
                    $updatedCount = $inputModel->editBulk($inputIds, ['block' => 'n']);
                    $msg = sprintf($this->view->translate('%d inputs were released.'), $updatedCount);
                } elseif ($this->getRequest()->getPost('blockBulk', null)) {
                    $updatedCount = $inputModel->editBulk($inputIds, ['block' => 'y']);
                    $msg = sprintf($this->view->translate('%d inputs were blocked.'), $updatedCount);
                } elseif ($this->getRequest()->getPost('enableVotingBulk', null)) {
                    $updatedCount = $inputModel->editBulk($inputIds, ['vot' => 'y']);
                    $msg = sprintf($this->view->translate('%d inputs were sent to voting.'), $updatedCount);
                } elseif ($this->getRequest()->getPost('blockVotingBulk', null)) {
                    $updatedCount = $inputModel->editBulk($inputIds, ['vot' => 'n']);
                    $msg = sprintf($this->view->translate('%d inputs were removed from voting.'), $updatedCount);
                } elseif ($this->getRequest()->getPost('deleteBulk', null)) {
                    $deletedCount = $inputModel->deleteBulk($inputIds);
                    $msg = sprintf($this->view->translate('%d inputs were deleted.'), $deletedCount);
                } elseif ($this->getRequest()->getPost('merge', null)) {
                    // This is awkward, but we need to utilise the same checkboxes as for bulk editing actions
                    // Essentially this only takes values from inputIds checkboxes and constructs an url to redirect to
                    return $this->redirect(
                        $this->view->url(['action' => 'merge', 'inputIds' => $this->getRequest()->getPost('inputIds', null)])
                    );
                }
                $this->_flashMessenger->addMessage($msg, 'success');
            } elseif ($this->getRequest()->getPost('delete', null)) {
                $inputModel->deleteById($this->getRequest()->getPost('delete', null));
                $this->_flashMessenger->addMessage('The input was deleted.', 'success');
            }
        }

        $this->redirect($this->view->url(['action' => 'overview']));
    }

    /**
     * Append inputs to another input and responds ajaxrequest in overview
     */
    public function appendinputsAction()
    {
        $this->_helper->layout()->disableLayout();
        $inputIDs = array();
        if (!empty($this->_params['inputIDs'])) {

            $inputIDs = explode(",", $this->_params['inputIDs']);
            $this->checkInputIDs($inputIDs);
            $pos = array_search($this->_tid, $inputIDs);
            if ($pos >= 0) {
                unset($inputIDs["$pos"]);
            }
            $inputIDs = implode(",", $inputIDs);

            $inputsModel = new Model_Inputs();
            $this->view->inputs = array();
            $this->view->inputs = $inputsModel->getAppendInputs($this->_tid, $inputIDs);
            if (!empty($this->view->inputs)) {
                $this->view->message = "Folgende Beiträge wurden hinzugefügt :  &#9660;";
            } else {
                $this->view->message = "Es wurden keine weiteren Beiträge hinzugefügt";
            }
        } else {
            $this->view->inputs = array();
            $this->view->message = "Es wurden keine Beiträge ausgewählt";
        }
    }

    /**
     *  Edits input
     */
    public function editAction()
    {
        if (empty($this->_tid)) {
            $this->_flashMessenger->addMessage('Kein Betrag ausgewählt', 'error');
            $this->_redirect(
                'admin/votingprepare/overview/kid/' . $this->_consultation['kid'] . '/qid/' . $this->_qid . ''
            );
        }

        $this->view->consultation = $this->_consultation;
        $inputModel = new Model_Inputs();
        $form = new Admin_Form_Input();

        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {
                $updated = $inputModel->updateById($this->_tid, $form->getValues());
                if ($updated == $this->_tid) {
                    $this->_flashMessenger->addMessage('Eintrag aktualisiert', 'success');
                } else {
                    $this->_flashMessenger->addMessage('Aktualisierung fehlgeschlagen', 'error');
                }
            } else {
                $this->_flashMessenger->addMessage('Bitte Eingaben prüfen!', 'error');
                $form->populate($data);
            }
        } else {
            $inputRow = $inputModel->getById($this->_tid);
            $form->populate($inputRow);
            if (!empty($inputRow['tags'])) {
                // gesetzte Tags als selected markieren
                $tagsSet = array();
                foreach ($inputRow['tags'] as $tag) {
                    $tagsSet[] = $tag['tg_nr'];
                }
                $form->setDefault('tags', $tagsSet);
            }
        }

        $this->view->assign(array('form' => $form, 'qid' => $this->_qid, 'tid' => $this->_tid));
    }

    /**
     * Inserts a new input from admin and set the old input as its child
     */
    public function splitAction()
    {
        $inputModel = new Model_Inputs();
        $form = new Admin_Form_Input();

        $inputId = $this->getRequest()->getParam('inputId');
        $this->addNewElements([$inputId], $form);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                $newTid = $inputModel->addInputs($postData);
                $this->_flashMessenger->addMessage('The new input was created.', 'success');
                $this->redirect($this->view->url());
            } else {
                $this->_flashMessenger->addMessage('Form invalid.', 'error');
            }
        }

        $this->view->inputs = $inputModel->fetchAllInputs(['tid = ?' => $inputId]);
        $this->view->consultation = $this->_consultation;
        $this->view->form = $form;
    }

    /**
     * Deletes inputs via ajax-link from related inputs
     * @see overviewAction()
     */
    public function delinputresponseAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            exit;  //no AjaxRequest
        }
        $this->_helper->layout()->disableLayout();
        $inputModel = new Model_Inputs();
        $inputTagsModel = new Model_InputsTags();

        $this->_tid = $this->getTId($this->_params);
        $inputTagsModel ->deleteByInputsId($this->_tid);

        // response
        if ($inputModel->deleteInputs($this->_tid)) {
            $this->view->response = "success";
        } else {
            $this->view->response = "error";
        }
    }

    /**
     * Selete shotcut to origin input via ajax-link from related inputs
     * @see overviewAction()
     */
    public function cancelshortcutresponseAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            exit;  //no AjaxRequest? exit!
        }
        $this->_helper->layout()->disableLayout();
        $this->_tid = $this->getTId($this->_params);
        $this->_child = (int)$this->_params["child"];

        $inputModel = new Model_Inputs();
        $input = $inputModel->getById($this->_tid);

        $relIDs= $input["rel_tid"];
        $relIDs=  explode(",", $relIDs);
        unset($relIDs[array_search($this->_child, $relIDs)]);  // delete given ID
        $relIDs=  implode(",", $relIDs);
        //update
        if ($inputModel->setAppendInputsByID($relIDs, $this->_tid)) {
            $this->view->response = "success";
        } else {
            $this->view->response = "error";
        }
    }


    /**
     * Gets a valid CSRF Protection Hash for Ajax-Form()
     * @see admin forms input.php
     */
    public function getnewhashAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            exit;  //no AjaxRequest
        }
        $this->_helper->layout()->disableLayout();
        $form = new Admin_Form_Input();
        $this->view->newhash = $form->getHash();
    }

    /**
     * Inserts a new input from admin and set the old inputs as childern
     */
    public function mergeAction()
    {
        $inputModel = new Model_Inputs();
        $inputIds = $this->getRequest()->getParam('inputIds');

        $form = new Admin_Form_Input();
        $this->addNewElements($inputIds, $form);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                $newTid = $inputModel->addInputs($postData);
                $this->_flashMessenger->addMessage('The new input was created.', 'success');
                $this->redirect($this->view->url(['action' => 'overview', 'inputIds' => null]));
            } else {
                $this->_flashMessenger->addMessage('Form invalid.', 'error');
            }
        }

        $this->view->inputs = $inputModel->fetchAllInputs(['tid IN (?)' => $inputIds]);
        $this->view->consultation = $this->_consultation;
        $this->view->form = $form;
    }

    /**
     * Creates a copy of an input
     */
    public function copyAction()
    {
        $inputModel = new Model_Inputs();
        $inputId = $this->getRequest()->getParam('inputId');

        $form = new Admin_Form_Input();
        $this->addNewElements([$inputId], $form);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData)) {
                $newTid = $inputModel->addInputs($postData);
                $this->_flashMessenger->addMessage('The input was copied. This is the copy.', 'success');
                $this->redirect(
                    $this->view->url(
                        ['controller' => 'input', 'action' => 'edit', 'tid' => $inputId, 'return' => 'votingprepare']
                    )
                );
            } else {
                $this->_flashMessenger->addMessage('Form invalid.', 'error');
            }
        } else {
            $origData = $inputModel->getById($inputId);
            $origData['uid'] = null;
            $origData['rel_id'] = $origData['tid'];
            unset($origData['when']);
            unset($origData['tid']);
            $form->populate($origData);
        }

        $this->view->consultation = $this->_consultation;
        $this->view->form = $form;
    }

    /**
     * Add hidden elements to form to allow for creating relations
     * @param array     $relTids  An array of related inputIds
     * @param Zend_Form $form    The form object
     */
    protected function addNewElements($relTids, Zend_Form $form)
    {
        $relTID = $form
            ->createElement('hidden', 'rel_tid')
            ->setValue(implode(',', $relTids));
        $form->addElement($relTID);

        $kid = $form
            ->createElement('hidden', 'kid')
            ->setValue($this->_consultation['kid']);
        $form->addElement($kid);
    }

    /**
     * Checks the values from array ganzzahlen
     **/
    protected function checkInputIDs($param)
    {
        $isDigit = new Zend_Validate_Digits();
        foreach ($param as $key => $value) {
            if (!$isDigit->isValid($value)) {
                $this->_flashMessenger->addMessage('Fehler, falsche Daten', 'error');
                $this->_redirect('/admin/votingprepare/error');
                break;

                return;
            }
        }
    }

    /**
     * Checks the kid and returns the values from DB if the consultation exists
     * @param get param kid
     * @return variables from consultation or votingprepare error
     */
    protected function getKid($params)
    {
        if (isset($params["kid"])) {
            $isDigit = new Zend_Validate_Digits();

            if ($params["kid"] > 0 && $isDigit->isValid($params["kid"])) {
                $consultationModel = new Model_Consultations();
                $this->_consultation = $consultationModel->getById($params["kid"]);
                if (count($this->_consultation) == 0) {
                    $this->_flashMessenger->addMessage('keine Beteiligungsrunde zu dieser ID vorhanden', 'error');
                    $this->_redirect('/admin/votingprepare/error');
                } else {
                    return $this->_consultation;
                }
            } else {
                $this->_flashMessenger->addMessage('ID der Beteiligungsrunde ungültig', 'error');
                $this->_redirect('/admin/votingprepare/error');
            }
        }
    }

    /**
     * Checks the qid
     * @param qid, get param
     * @return (int)
     */
    protected function getQid($params)
    {
        if (isset($params["qid"])) {
            $isDigit = new Zend_Validate_Digits();
            if ($params["qid"] > 0 && $isDigit->isValid($params["qid"])) {
                return (int) $params["qid"];
            } else {
                $this->_flashMessenger->addMessage('QuestionID ungültig', 'error');
                $this->_redirect('/admin/votingprepare/error');
            }
        }
    }

    /**
     * Checks the id
     * @param dir, get param
     * @return (int)
     */
    protected function getDirId($params)
    {
        if (isset($params["dir"])) {
            $isDigit = new Zend_Validate_Digits();
            if ($params["dir"] > 0 && $isDigit->isValid($params["dir"])) {
                return (int) $params["dir"];
            } else {
                $this->_flashMessenger->addMessage('DirectoryID ungültig', 'error');
                $this->_redirect('/admin/votingprepare/error');
            }
        }
    }

    /**
     * Checks the tid
     * @param tid, get params
     * @return (int)
     */
    protected function getTId($params)
    {
        if (isset($params["tid"])) {
            $isDigit = new Zend_Validate_Digits();
            if ($params["tid"] > 0 && $isDigit->isValid($params["tid"])) {
                return (int) $params["tid"];
            } else {
                $this->_flashMessenger->addMessage('Thesen ID ungültig', 'error');
                $this->_redirect('/admin/votingprepare/error');
            }
        }
    }
}
