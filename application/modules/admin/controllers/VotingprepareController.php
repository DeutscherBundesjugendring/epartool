<?php

class Admin_VotingprepareController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
    protected $_consultation = null;
    protected $_question = null;

    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_params = $this->_request->getParams();
        $this->_consultation = $this->getKid($this->_params);
        $this->_question = $this->getQid($this->_params);
        if (isset($this->_params["tid"])) {
            $this->_tid = $this->getTId($this->_params);
        }
    }

    /**
     * Place-maker for error redirects messages from flashmessenger
     */
    public function errorAction()
    {
    }

    /**
     * Returns parameters for list of questions in backend
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
        $dirs = array();
        $directories = new Model_Directories();
        $dirs = $directories
            ->getTree(
                "node.kid = " . $this->_consultation['kid'] . " AND parent.kid = " . $this->_consultation['kid'] . ""
            )
            ->toArray();

        $questionModel = new Model_Questions();
        $inputsModel = new Model_Inputs();
        $tagModel = new Model_Tags();

        foreach ($dirs as $key => $value) {
            $dirs["$key"]['count'] = $inputsModel->getNumByDirectory(
                $this->_consultation['kid'],
                $this->_question,
                $dirs["$key"]['id']
            );
            $dirs["$key"]['qid'] = $this->_question;
        }
        $options = array('kid' => $this->_consultation["kid"], 'qid' => $this->_question);
        $options['dir'] = $this->getDirId($this->_params);

        $tags = $tagModel->getAll()->toArray();

        if (isset($this->_params['tags'])) {
            $this->checkInputIDs($this->_params['tags']);
            $options['tags'] = $this->_params['tags'];
            foreach ($tags as $key => $value) {
                if (in_array($value['tg_nr'], $options['tags'])) {
                    $tags["$key"]['selected'] = 1;
                } else {
                    $tags["$key"]['selected'] = '0';
                }
            }
        }

        if (isset($this->_params['search-phrase'])) {
            if (isset($this->_params['combine']) && $this->_params['combine'] == 'AND') {
                $options['combine'] = 'AND';
            } else {
                $options['combine'] = 'OR';
            }
            if (isset($this->_params['directory']) && $this->_params['directory'] == '0') {
                $options['dir'] = '0';
            } else {
                $options['dir'] = $options['dir'];
            }
            $options['search-phrase'] = trim($this->_params['search-phrase']);
        }

        $this->view->inputs = array();
        $this->view->question = array();
        $this->view->consultation = array();
        $this->view->directories = array();
        $this->view->tags = array();
        $this->view->directoryact = "keine Auswahl";

        foreach ($dirs as $key=>$value) {
            if ($value["id"]==$options['dir']) {
                $this->view->directoryact = $value["dir_name"];
                break;
            }
        }

        $this->view->inputs = $inputsModel->fetchAllInputs($options);
        $this->view->question = $questionModel->find($this->_question)->current();
        $this->view->consultation = $this->_consultation;
        $this->view->directories = $dirs;
        $this->view->tags = $tags;
        $this->view->directory = $options['dir'];
        $this->view->getParams = 'kid/' . $this->view->consultation['kid'] . '/qid/' . $this->view->question['qi'];
        if (isset($this->view->directory))
            $this->view->getParams = $this->view->getParams . '/dir/' . $this->view->directory;
        if (isset($options['search-phrase'])) {
            $this->view->searchphrase = $options['search-phrase'];
        } else {
            $this->view->searchphrase = "";
        }
        (isset($options['combine'])) ? $this->view->combine = $options['combine'] : $this->view->combine = 'OR';
        (isset($options['dir'])) ? $this->view->dirs = $options['dir'] : $this->view->dirs = '';
    }

    /**
     * Updates the status of an input for voting and responds ajaxrequest from overviewAction
     */
    public function votingstatusAction()
    {
        $this->_helper->layout()->disableLayout();
        $inputsModel = new Model_Inputs();
        $this->input = $inputsModel->find($this->_tid)->current();
        switch ($this->input->vot) {
            case 'y':
                $status = "u";
                $inputsModel->setVotingStatusByID($status, $this->_tid);
                break;
            case 'n':
                $status = "y";
                $inputsModel->setVotingStatusByID($status, $this->_tid);
                break;
            case 'u':
                $status = "n";
                $inputsModel->setVotingStatusByID($status, $this->_tid);
                break;
        }
        $this->view->vot = $status;
    }

    /**
     * Updates the status of an input for public viewing and responds  ajaxrequest in overview
     */
    public function blockstatusAction()
    {
        $this->_helper->layout()->disableLayout();
        $inputsModel = new Model_Inputs();
        $this->input = $inputsModel->find($this->_tid)->current();
        switch ($this->input->block) {
            case 'y':
                $status = "u";
                $inputsModel->setBlockStatusByID($status, $this->_tid);
                break;
            case 'n':
                $status = "y";
                $inputsModel->setBlockStatusByID($status, $this->_tid);
                break;
            case 'u':
                $status = "n";
                $inputsModel->setBlockStatusByID($status, $this->_tid);
                break;
        }
        $this->view->block = $status;
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
            . $this->_question
            . '/dir/'
            . $this->getDirId($this->_params)
        );
    }

    /**
     * Updates votingstatus, blockstatus or delete inputs and redirect to overviewAction
     */
    public function updateAction()
    {
        $this->_helper->layout()->disableLayout();
        if (!empty($this->_params['thesis'])) {
            $this->checkInputIDs($this->_params['thesis']);
            $option = implode(",", $this->_params['thesis']);

            $inputsModel = new Model_Inputs();
            switch ($this->_params['do']) {
                case 'enable':
                    $inputsModel->setBlockStatus($option, 'y');
                    $this->_flashMessenger->addMessage('Die markierten Beiträge wurden zur Anzeige freigegeben', 'success');
                    break;
                case 'disable':
                    $inputsModel->setBlockStatus($option, 'n');
                    $this->_flashMessenger->addMessage('Die markierten Beiträge wurden zur Anzeige gesperrt', 'success');
                    break;
                case 'enable-voting':
                    $inputsModel->setVotingStatus($option, 'y');
                    $this->_flashMessenger->addMessage('Die markierten Beiträge wurden zum Voting freigegeben', 'success');
                    break;
                case 'disable-voting':
                    $inputsModel->setVotingStatus($option, 'n');
                    $this->_flashMessenger->addMessage('Die markierten Beiträge wurden zum Voting  gesperrt', 'success');
                    break;
                case 'delete':
                    $inputsModel->deleteInputs($option);
                    $inputTagsModel = new Model_InputsTags();
                    foreach ($this->_params['thesis'] as $key => $value) {
                        $inputTagsModel ->deleteByInputsId($value);
                    }
                    $this->_flashMessenger->addMessage('Die markierten Beiträge wurden gelöscht', 'success');
                    break;
                default:
                    $this->_flashMessenger->addMessage('Keine Aktion!', 'error');
                    $this->_redirect('/admin/votingprepare/error');
            }
        } else {
            $this->_flashMessenger->addMessage('Es wurden keine Beiträge ausgewählt', 'error');
        }
        if (isset($params["dir"])) {
            $this->redirect(
                '/admin/votingprepare/overview/kid/' . $this->_consultation["kid"]
                . '/qid/' . $this->_question
                . '/dir/' . $this->getDirId($this->_params)
            );
        } else {
            $this->redirect(
                '/admin/votingprepare/overview/kid/' . $this->_consultation["kid"] . '/qid/' . $this->_question
            );
        }
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
                'admin/votingprepare/overview/kid/' . $this->_consultation['kid'] . '/qid/' . $this->_question . ''
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

        $this->view->assign(array('form' => $form, 'qid' => $this->_question, 'tid' => $this->_tid));
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
                'admin/votingprepare/overview/kid/' . $this->_consultation['kid'] . '/qid/' . $this->_question . ''
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
            'qid' => $this->_question,
            'dir' => $directory,
            'inputIDs' => array($this->_tid)
        );
        $this->view->inputs = $inputModel->fetchAllInputs($options);
        $this->view->consultation = $this->_consultation;
        $this->view->assign(array('form' => $form, 'qid' => $this->_question));
        $this->view->getParams =
            'kid/' . $this->view->consultation['kid']
            . '/qid/' . $this->_question . '/tid/' . $this->_tid;
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
                            . '/qid/' . $this->_question
                            . '/dir/' . $this->getDirId($this->_params)
                        );
                    } else {
                        $this->redirect(
                            '/admin/votingprepare/overview/kid/' . $this->_consultation["kid"] . '/qid/' . $this->_question
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
            'qid' => $this->_question,
            'dir' => $directory,
            'inputIDs' => $inputIDs
        );

        $this->view->inputs = $inputModel->fetchAllInputs($options);
        $this->view->consultation = $this->_consultation;
        $this->view->assign(array('form' => $form, 'qid' => $this->_question));
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
                'admin/votingprepare/overview/kid/' . $this->_consultation['kid'] . '/qid/' . $this->_question . ''
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
            . $this->_question
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
