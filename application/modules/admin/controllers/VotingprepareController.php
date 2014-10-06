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

        $fulltext = $this->getRequest()->getParam('fulltext', null);
        $wheres = [
            'qid' => $this->_qid,
            'fulltext' => $fulltext,
        ];

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
    public function updateAction()
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
     * Gets the input wich will be splitt and a ajay-form for new inputs
     * @see VotingprepareController|admin: splitresponseAction()
     */
    public function splitAction()
    {
        if (empty($this->_tid)) {
            $this->_flashMessenger->addMessage('Kein Betrag ausgewählt', 'error');
            $this->_redirect(
                'admin/votingprepare/overview/kid/' . $this->_consultation['kid'] . '/qid/' . $this->_qid . ''
            );
        }

        if (isset($this->_params['dir']) && !empty($this->_params['dir'])) {
            $directory = $this->getDirId($this->_params);
        } else {
            $directory = 0;
        }

        $inputModel = new Model_Inputs();
        $form = new Admin_Form_Input();
        $options = array(
            'directory' => $directory,
            'relTID' => "",
            'uid' => null,
            'inputs' => $this->_tid,
            'kid' => $this->_consultation['kid']
        );
        $this->addNewElements($options, $form);

        $options = array(
            'kid' => $this->_consultation['kid'],
            'qid' => $this->_qid,
            'dir' => $directory,
            'inputIDs' => array($this->_tid)
        );
        $this->view->inputs = $inputModel->fetchAllInputs($options);
        $this->view->consultation = $this->_consultation;
        $this->view->assign(array('form' => $form, 'qid' => $this->_qid));
        $this->view->getParams =
            'kid/' . $this->view->consultation['kid']
            . '/qid/' . $this->_qid . '/tid/' . $this->_tid;
        if (isset($this->view->directory)) {
            $this->view->getParams = $this->view->getParams . '/dir/' . $this->view->directory;
        }
    }

    /**
     * Gets the response for splitAction()
     * @see VotingprepareController|admin: splitAction()
     */
    public function splitresponseAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            exit; //no AjaxRequest
        }

        $this->_helper->layout()->disableLayout();
        $data = $this->_request->getPost();
        $data['uid'] = null;

        $form = new Admin_Form_Input();

        if ($form->isValid($data)) {
            $inputModel = new Model_Inputs();
            $insert = $inputModel->addInputs($data);
            if (!empty($insert)) {
                $inputIDs = $insert['tid'];
                $relIDs = $inputModel->getAppendInputs($this->_tid, $inputIDs);
                $this->view->response = "success";
                $this->view->inputs = $insert;
            } else {
                $this->view->response = "error";
            }
        }
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
     * Inserts a new input from admin set the old inputs as childs
     */
    public function mergeAction()
    {
        if (isset($this->_params['dir']) && !empty($this->_params['dir'])) {
            $directory = $this->getDirId($this->_params);
        } else {
            $directory = 0;
        }

        $inputIDs = explode(",", $this->_params['inputs']);
        $this->checkInputIDs($inputIDs);

        $inputModel = new Model_Inputs();
        $form = new Admin_Form_Input();
        $options = array(
            'directory' => $directory,
            'relTID' => $this->_params['inputs'],
            'uid' => null,
            'kid' => $this->_consultation['kid']
        );
        $this->addNewElements($options, $form);
        $form->removeElement('uid');

        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {
                $insert = $inputModel->addInputs($data);
                if (!empty($insert)) {
                    $this->_flashMessenger->addMessage('Der Redaktionsbeitrag wurde hinzugefügt', 'success');
                    if (isset($params["dir"])) {
                        $this->redirect(
                            '/admin/votingprepare/overview/kid/' . $this->_consultation["kid"]
                            . '/qid/' . $this->_qid
                            . '/dir/' . $this->getDirId($this->_params)
                        );
                    } else {
                        $this->redirect(
                            '/admin/votingprepare/overview/kid/' . $this->_consultation["kid"] . '/qid/' . $this->_qid
                        );
                    }
                } else {
                    $this->_flashMessenger->addMessage('Fehler beim eintragen ses Redaktionsbeitrages', 'error');
                }
            } else {
                $this->_flashMessenger->addMessage('Bitte Eingaben prüfen!', 'error');
                $form->populate($data);
            }
        }
        $options = array(
            'kid' => $this->_consultation['kid'],
            'qid' => $this->_qid,
            'dir' => $directory,
            'inputIDs' => $inputIDs
        );

        $this->view->inputs = $inputModel->fetchAllInputs($options);
        $this->view->consultation = $this->_consultation;
        $this->view->assign(array('form' => $form, 'qid' => $this->_qid));
    }

    /**
     * Copies an input
     */
    public function copyAction()
    {
        $this->_helper->layout()->disableLayout();
        if (empty($this->_tid)) {
            $this->_flashMessenger->addMessage('Kein Betrag ausgewählt', 'error');
            $this->_redirect(
                'admin/votingprepare/overview/kid/' . $this->_consultation['kid'] . '/qid/' . $this->_qid . ''
            );
        }

        $inputModel = new Model_Inputs();
        $inputTagsModel = new Model_InputsTags();

        // Get the data from the database - source row
        $copyprepare = $inputModel->getById($this->_tid);
        $tags= $copyprepare['tags'];

        // New Owner (Admin)
        $copyprepare['uid'] = 1;
        // New create_date
        $copyprepare['when'] = "";

        // Unset the uid which should be the primary key and teh tag array()
        unset ($copyprepare['tags']);
        unset ($copyprepare['tid']);

         //insert
        $new = $inputModel ->add($copyprepare);

        //insert tags when have
        if (!empty($tags)) {
            foreach ($tags as $key => $value) {
                $inputTagsModel->insertByInputsId($new, array($value["tg_nr"]));
            }
        }

        //redirect to editaction
        $this->redirect(
            '/admin/votingprepare/edit/kid/'
            . $this->_consultation["kid"]
            . '/qid/'
            . $this->_qid
            . '/tid/'
            . $new
            . '/dir/'
            . $this->getDirId($this->_params)
        );
    }

    /**
     * Add hidden and default elements to Inputformular
     */
    protected function addNewElements($options, $form)
    {
        $dir = $form
            ->createElement('hidden', 'dir')
            ->removeDecorator('DtDdWrapper')
            ->removeDecorator('HtmlTag')
            ->removeDecorator('Label')
            ->setValue($options['directory'])
            ->addvalidator('NotEmpty', $breakChainOnFailure = true)
            ->addvalidator('Int', $breakChainOnFailure = true)
            ->setRequired(true);
        $form->addElement($dir);

        $relTID = $form
            ->createElement('hidden', 'rel_tid')
            ->removeDecorator('DtDdWrapper')
            ->removeDecorator('HtmlTag')
            ->removeDecorator('Label')
            ->setValue($options['relTID']);
        $form->addElement($relTID);

        $uid = $form
            ->createElement('hidden', 'uid')
            ->removeDecorator('DtDdWrapper')
            ->removeDecorator('HtmlTag')
            ->removeDecorator('Label')
            ->setValue($options['uid'])
            ->addvalidator('NotEmpty', $breakChainOnFailure = false)
            ->addvalidator('Int', $breakChainOnFailure = true)
            ->setRequired(true);
        $form->addElement($uid);

        $kid = $form
            ->createElement('hidden', 'kid')
            ->removeDecorator('DtDdWrapper')
            ->removeDecorator('HtmlTag')
            ->removeDecorator('Label')
            ->setValue($options['kid'])
            ->addvalidator('NotEmpty', $breakChainOnFailure = true)
            ->addvalidator('Int', $breakChainOnFailure = true)
            ->setRequired(true);
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
