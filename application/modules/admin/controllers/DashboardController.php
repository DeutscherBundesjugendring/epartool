<?php
/**
 * class for handling inputs in backend dashboard
 *
 * @author		Karsten Tackmann <info@seitenmeister.com>
 */
class Admin_DashboardController extends Zend_Controller_Action {

	protected $_flashMessenger = null;
	protected $_consultation = null;
	protected $_question = null;

	public function init() {
		$this -> _helper -> layout -> setLayout('backend');
		$this -> _flashMessenger = $this -> _helper -> getHelper('FlashMessenger');
		$this -> _params = $this -> _request -> getParams();
		$this -> _consultation = $this -> getKid($this -> _params);
		$this -> _question = $this -> getQid($this -> _params);
		if (isset($this -> _params["tid"]))
			$this -> _tid = $this -> getTId($this -> _params);
	}

	/**
	 *  errorAction
	 * place-maker for error redirects messages from flashmessenger
	 * @return array
	 *
	 **/
	public function errorAction() {

	}

	/**
	 *  indexAction
	 * get parameters for list of questions in backend
	 * @see DashboardController|admin: init()
	 * @return array
	 *
	 **/
	public function indexAction() {
		$this -> view -> consultation = $this -> _consultation;
	}

	/**
	 *  overviewAction
	 * get parameters for list of inputs, questions and directories
	 * @return array
	 *
	 **/
	public function overviewAction() {

		$dirs = array();
		$directories = new Model_Directories();
		$dirs = $directories -> getTree("node.kid = " . $this -> _consultation['kid'] . " AND parent.kid = " . $this -> _consultation['kid'] . "") -> toArray();

		$questionModel = new Model_Questions();
		$inputsModel = new Model_Inputs();
		$tagModel = new Model_Tags();

		foreach ($dirs as $key => $value) {
			$dirs["$key"]['count'] = $inputsModel -> getNumByDirectory($this -> _consultation['kid'], $this -> _question, $dirs["$key"]['id']);
			$dirs["$key"]['qid'] = $this -> _question;
		}

		$options = array('kid' => $this -> _consultation["kid"], 'qid' => $this -> _question);
		$options['dir'] = $this -> getDirId($this -> _params);

		$tags = $tagModel -> getAll() -> toArray();

		if (isset($this -> _params['tags'])) {
			$this -> checkInputIDs($this -> _params['tags']);
			$options['tags'] = $this -> _params['tags'];
			foreach ($tags as $key => $value) {
				(in_array($value['tg_nr'], $options['tags'])) ? $tags["$key"]['selected'] = 1 : $tags["$key"]['selected'] = '0';
			}
		}

		if (isset($this -> _params['search-phrase'])) {
			(isset($this -> _params['combine']) && $this -> _params['combine'] == 'AND') ? $options['combine'] = 'AND' : $options['combine'] = 'OR';
			(isset($this -> _params['directory']) && $this -> _params['directory'] == '0') ? $options['dir'] = '0' : $options['dir'] = $options['dir'];
			$options['search-phrase'] = trim($this -> _params['search-phrase']);
		}

		$this -> view -> inputs = array();
		$this -> view -> question = array();
		$this -> view -> consultation = array();
		$this -> view -> directories = array();
		$this -> view -> tags = array();

		$this -> view -> inputs = $inputsModel -> fetchAllInputs($options);
		$this -> view -> question = $questionModel -> find($this -> _question) -> current();
		$this -> view -> consultation = $this -> _consultation;
		$this -> view -> directories = $dirs;
		$this -> view -> tags = $tags;
		$this -> view -> directory = $options['dir'];
		$this -> view -> getParams = 'kid/' . $this -> view -> consultation['kid'] . '/qid/' . $this -> view -> question['qi'];
		if (isset($this -> view -> directory))
			$this -> view -> getParams = $this -> view -> getParams . '/dir/' . $this -> view -> directory;
		(isset($options['search-phrase'])) ? $this -> view -> searchphrase = $options['search-phrase'] : $this -> view -> searchphrase = "";
		(isset($options['combine'])) ? $this -> view -> combine = $options['combine'] : $this -> view -> combine = 'OR';
		(isset($options['dir'])) ? $this -> view -> dirs = $options['dir'] : $this -> view -> dirs = '';
		#$this -> view -> combine = $options['combine'];
	}

	/**
	 * votingstatusAction
	 * updates the status of an input for voting and responds ajaxrequest from overviewAction
	 * @return array
	 *
	 **/
	public function votingstatusAction() {
		$this -> _helper -> layout() -> disableLayout();
		$inputsModel = new Model_Inputs();
		$this -> input = $inputsModel -> find($this -> _tid) -> current();
		// echo "<pre>";
		// print_r($this -> input -> vot);
		// echo "<pre>";
		switch ($this->input->vot) {
			case 'y' :
				$status = "u";
				$inputsModel -> setVotingStatusByID($status, $this -> _tid);
				break;
			case 'n' :
				$status = "y";
				$inputsModel -> setVotingStatusByID($status, $this -> _tid);
				break;
			case 'u' :
				$status = "n";
				$inputsModel -> setVotingStatusByID($status, $this -> _tid);
				break;
		}
		$this -> view -> vot = $status;
	}

	/**
	 * blockstatusAction
	 * updates the status of an input for public viewing and responds  ajaxrequest in overview
	 * @return array
	 *
	 **/
	public function blockstatusAction() {
		$this -> _helper -> layout() -> disableLayout();
		$inputsModel = new Model_Inputs();
		$this -> input = $inputsModel -> find($this -> _tid) -> current();
		// echo "<pre>";
		// print_r($this -> input -> vot);
		// echo "<pre>";
		switch ($this->input->block) {
			case 'y' :
				$status = "u";
				$inputsModel -> setBlockStatusByID($status, $this -> _tid);
				break;
			case 'n' :
				$status = "y";
				$inputsModel -> setBlockStatusByID($status, $this -> _tid);
				break;
			case 'u' :
				$status = "n";
				$inputsModel -> setBlockStatusByID($status, $this -> _tid);
				break;
		}
		$this -> view -> block = $status;
	}

	/**
	 *  setdirectoryAction
	 * updates the directory for given inputs and redirect to overviewAction
	 * @return string (flashMessenger)
	 *
	 **/
	public function setdirectoryAction() {

		$this -> _helper -> layout() -> disableLayout();

		if (!empty($this -> _params['thesis'])) {

			$this -> checkInputIDs($this -> _params['thesis']);

			$options = array();
			$options['dir'] = $this -> getDirId($this -> _params);
			$options['thesis'] = implode(",", $this -> _params['thesis']);

			$inputsModel = new Model_Inputs();
			$inputsModel -> setDirectory($options);
			$this -> _flashMessenger -> addMessage('Die markierten Beiträge wurden verschoben', 'success');

		} else {
			$this -> _flashMessenger -> addMessage('Es wurden keine Beiträge ausgewählt', 'error');
		}
		$this -> redirect(
					'/admin/dashboard/overview/kid/' . $this -> _consultation["kid"] . '/qid/' . $this -> _question . '/dir/' . $this -> getDirId($this -> _params));
	}

	/**
	 *  updateAction()
	 * updates votingstatus, blockstatus or delete inputs and redirect to overviewAction
	 * @return string (flashMessenger)
	 *
	 **/
	public function updateAction() {

		$this -> _helper -> layout() -> disableLayout();
		if (!empty($this -> _params['thesis'])) {
			$this -> checkInputIDs($this -> _params['thesis']);
			$option = implode(",", $this -> _params['thesis']);

			$inputsModel = new Model_Inputs();
			switch ($this -> _params['do']) {
				case 'enable' :
					$inputsModel -> setBlockStatus($option, 'y');
					$this -> _flashMessenger -> addMessage('Die markierten Beiträge wurden zur Anzeige freigegeben', 'success');
					break;
				case 'disable' :
					$inputsModel -> setBlockStatus($option, 'n');
					$this -> _flashMessenger -> addMessage('Die markierten Beiträge wurden zur Anzeige gesperrt', 'success');
					break;
				case 'enable-voting' :
					$inputsModel -> setVotingStatus($option, 'y');
					$this -> _flashMessenger -> addMessage('Die markierten Beiträge wurden zum Voting freigegeben', 'success');
					break;
				case 'disable-voting' :
					$inputsModel -> setVotingStatus($option, 'n');
					$this -> _flashMessenger -> addMessage('Die markierten Beiträge wurden zum Voting  gesperrt', 'success');
					break;
				case 'delete' :
					$inputsModel -> deleteInputs($option);
					$this -> _flashMessenger -> addMessage('Die markierten Beiträge wurden gelöscht', 'success');
					break;
				default :
					$this -> _flashMessenger -> addMessage('Keine Aktion!', 'error');
					$this -> _redirect('/admin/dashboard/error');
			}
		} else {
			$this -> _flashMessenger -> addMessage('Es wurden keine Beiträge ausgewählt', 'error');
		}
		if (isset($params["dir"])) {
			$this -> redirect('/admin/dashboard/overview/kid/' . $this -> _consultation["kid"] . '/qid/' . $this -> _question . '/dir/' . $this -> getDirId($this -> _params));
		} else {
			$this -> redirect('/admin/dashboard/overview/kid/' . $this -> _consultation["kid"] . '/qid/' . $this -> _question);
		}
	}

	/**
	 *  appendinputsAction
	 *  append inputs to another input and responds ajaxrequest in overview
	 * @return array
	 *
	 **/
	public function appendinputsAction() {
		$this -> _helper -> layout() -> disableLayout();
		$inputIDs = array();
		if (!empty($this -> _params['inputIDs'])) {

			$inputIDs = explode(",", $this -> _params['inputIDs']);
			$this -> checkInputIDs($inputIDs);
			$pos = array_search($this -> _tid, $inputIDs);
			if ($pos >= 0)
				unset($inputIDs["$pos"]);
			$inputIDs = implode(",", $inputIDs);

			$inputsModel = new Model_Inputs();
			$this -> view -> inputs = array();
			$this -> view -> inputs = $inputsModel -> getAppendInputs($this -> _tid, $inputIDs);
			(!empty($this -> view -> inputs)) ? $this -> view -> message = "Folgende Beiträge wurden hinzugefügt :  &#9660;" : $this -> view -> message = "Es wurden keine weiteren Beiträge hinzugefügt";
		} else {
			$this -> view -> inputs = array();
			$this -> view -> message = "Es wurden keine Beiträge ausgewählt";
		}
	}

	/**
	 *  editAction()
	 *  edit input
	 * @param get param
	 * @return bool or redirect dashboard error
	 *
	 **/
	public function editAction() {

		if (empty($this -> _tid)) {
			$this -> _flashMessenger -> addMessage('Kein Betrag ausgewählt', 'error');
			$this -> _redirect('admin/dashboard/overview/kid/' . $this -> _consultation['kid'] . '/qid/' . $this -> _question . '');
		}

		$this -> view -> consultation = $this -> _consultation;
		$inputModel = new Model_Inputs();
		$form = new Admin_Form_Input();

		if ($this -> _request -> isPost()) {
			$data = $this -> _request -> getPost();
			if ($form -> isValid($data)) {
				$updated = $inputModel -> updateById($this -> _tid, $form -> getValues());
				if ($updated == $this -> _tid) {
					$this -> _flashMessenger -> addMessage('Eintrag aktualisiert', 'success');
				} else {
					$this -> _flashMessenger -> addMessage('Aktualisierung fehlgeschlagen', 'error');
				}
			} else {
				$this -> _flashMessenger -> addMessage('Bitte Eingaben prüfen!', 'error');
				$form -> populate($data);
			}
		} else {
			$inputRow = $inputModel -> getById($this -> _tid);
			$form -> populate($inputRow);
			if (!empty($inputRow['tags'])) {
				// gesetzte Tags als selected markieren
				$tagsSet = array();
				foreach ($inputRow['tags'] as $tag) {
					$tagsSet[] = $tag['tg_nr'];
				}
				$form -> setDefault('tags', $tagsSet);
			}
		}

		$this -> view -> assign(array('form' => $form, 'qid' => $this -> _question, 'tid' => $this -> _tid));
	}

	/**
	 *  splitAction()
	 *  gets the input wich will be splitt and a ajay-form for new inputs
	 * @see DashboardController|admin: splitresponseAction()
	 * @param get param
	 * @return bool or redirect dashboard error
	 *
	 **/
	public function splitAction() {

		if (empty($this -> _tid)) {
			$this -> _flashMessenger -> addMessage('Kein Betrag ausgewählt', 'error');
			$this -> _redirect('admin/dashboard/overview/kid/' . $this -> _consultation['kid'] . '/qid/' . $this -> _question . '');
		}

		(isset($this -> _params['dir']) && !empty($this -> _params['dir'])) ? $directory = $this -> getDirId($this -> _params) : $directory = 0;

		$inputModel = new Model_Inputs();
		$form = new Admin_Form_Input();
		$options = array('directory' => $directory, 'relTID' => "", 'uid' => 1, 'inputs' => $this -> _tid, 'kid' => $this -> _consultation['kid']);
		$this -> addNewElements($options, $form);

		$options = array('kid' => $this -> _consultation['kid'], 'qid' => $this -> _question, 'dir' => $directory, 'inputIDs' => array($this -> _tid));
		$this -> view -> inputs = $inputModel -> fetchAllInputs($options);
		$this -> view -> consultation = $this -> _consultation;
		$this -> view -> assign(array('form' => $form, 'qid' => $this -> _question));
		$this -> view -> getParams = 'kid/' . $this -> view -> consultation['kid'] . '/qid/' . $this -> _question . '/tid/' . $this -> _tid;
		if (isset($this -> view -> directory))
			$this -> view -> getParams = $this -> view -> getParams . '/dir/' . $this -> view -> directory;

	}

	/**
	 *  splitresponseAction()
	 *  gets the response for splitAction()
	 * @see DashboardController|admin: splitAction()
	 * @param get param
	 * @return bool or redirect dashboard error
	 *
	 **/
	public function splitresponseAction() {
		$this -> _helper -> layout() -> disableLayout();
		$data = $this -> _request -> getPost();

		$form = new Admin_Form_Input();

		if ($form -> isValid($data)) {
			$inputModel = new Model_Inputs();
			$insert = $inputModel -> addInputs($data);
			if (!empty($insert)) {
				$inputIDs = $insert['tid'];
				$relIDs = $inputModel ->getAppendInputs($this -> _tid,$inputIDs) ;
				$this -> view -> response = "success";
				$this -> view -> inputs = $insert;
			} else {
				$this -> view -> response = "error";
			}
		}
	}

	/**
	 *  mergeAction()
	 *  inserts a new input from admin set the old inputs as childs
	 * @param get param
	 * @return bool or redirect dashboard error
	 *
	 **/
	public function mergeAction() {

		(isset($this -> _params['dir']) && !empty($this -> _params['dir'])) ? $directory = $this -> getDirId($this -> _params) : $directory = 0;

		$inputIDs = explode(",", $this -> _params['inputs']);
		$this -> checkInputIDs($inputIDs);

		$inputModel = new Model_Inputs();
		$form = new Admin_Form_Input();
		#$options = array('directory' => $directory, 'relTID' => $inputIDs, 'uid' => 1, 'inputs' => $this -> _params['inputs'], 'kid' => $this -> _consultation['kid']);
		$options = array('directory' => $directory, 'relTID' => $this -> _params['inputs'], 'uid' => 1, 'kid' => $this -> _consultation['kid']);
		$this -> addNewElements($options, $form);

		if ($this -> _request -> isPost()) {
			$data = $this -> _request -> getPost();
			if ($form -> isValid($data)) {
				$insert = $inputModel -> addInputs($data);
				if (!empty($insert)) {
					$this -> _flashMessenger -> addMessage('Der Redaktionsbeitrag wurde hinzugefügt', 'success');
					if (isset($params["dir"])) {
						$this -> redirect('/admin/dashboard/overview/kid/' . $this -> _consultation["kid"] . '/qid/' . $this -> _question . '/dir/' . $this -> getDirId($this -> _params));
					} else {
						$this -> redirect('/admin/dashboard/overview/kid/' . $this -> _consultation["kid"] . '/qid/' . $this -> _question);
					}
				} else {
					$this -> _flashMessenger -> addMessage('Fehler beim eintragen ses Redaktionsbeitrages', 'error');
				}
			} else {
				$this -> _flashMessenger -> addMessage('Bitte Eingaben prüfen!', 'error');
				$form -> populate($data);
			}
		}
		$options = array('kid' => $this -> _consultation['kid'], 'qid' => $this -> _question, 'dir' => $directory, 'inputIDs' => $inputIDs);

		$this -> view -> inputs = $inputModel -> fetchAllInputs($options);
		$this -> view -> consultation = $this -> _consultation;
		$this -> view -> assign(array('form' => $form, 'qid' => $this -> _question));
	}

	/**
	 *  addNewElements
	 *  add hidden and default elements to Inputformular
	 * @param options
	 * @return bool or redirect dashboard error
	 *
	 **/
	protected function addNewElements($options, $form) {
		$dir = $form -> createElement('hidden', 'dir') -> removeDecorator('DtDdWrapper') -> removeDecorator('HtmlTag') -> removeDecorator('Label') -> setValue($options['directory']) -> addvalidator('NotEmpty', $breakChainOnFailure = true) -> addvalidator('Int', $breakChainOnFailure = true) -> setRequired(true);
		$form -> addElement($dir);

		$relTID = $form -> createElement('hidden', 'rel_tid') -> removeDecorator('DtDdWrapper') -> removeDecorator('HtmlTag') -> removeDecorator('Label') -> setValue($options['relTID']);
		$form -> addElement($relTID);

		$uid = $form -> createElement('hidden', 'uid') -> removeDecorator('DtDdWrapper') -> removeDecorator('HtmlTag') -> removeDecorator('Label') -> setValue($options['uid']) -> addvalidator('NotEmpty', $breakChainOnFailure = false) -> addvalidator('Int', $breakChainOnFailure = true) -> setRequired(true);
		$form -> addElement($uid);

		$kid = $form -> createElement('hidden', 'kid') -> removeDecorator('DtDdWrapper') -> removeDecorator('HtmlTag') -> removeDecorator('Label') -> setValue($options['kid']) -> addvalidator('NotEmpty', $breakChainOnFailure = true) -> addvalidator('Int', $breakChainOnFailure = true) -> setRequired(true);
		$form -> addElement($kid);
	}

	/**
	 *  checkInputIDs
	 *  checks the values  from array ganzzahlen
	 * @param get param
	 * @return bool or redirect dashboard error
	 *
	 **/
	protected function checkInputIDs($param) {
		$isDigit = new Zend_Validate_Digits();
		foreach ($param as $key => $value) {
			if (!$isDigit -> isValid($value)) {
				$this -> _flashMessenger -> addMessage('Fehler, falsche Daten', 'error');
				$this -> _redirect('/admin/dashboard/error');
				break;
				return;
			}
		}
	}

	/**
	 * getKid
	 * checks the kid and returns the values from DB if the consultation exists
	 * @param get param kid
	 * @return variables from consultation or dashboard error
	 *
	 **/
	protected function getKid($params) {
		if (isset($params["kid"])) {
			$isDigit = new Zend_Validate_Digits();

			if ($params["kid"] > 0 && $isDigit -> isValid($params["kid"])) {
				$consultationModel = new Model_Consultations();
				$this -> _consultation = $consultationModel -> getById($params["kid"]);
				if (count($this -> _consultation) == 0) {
					$this -> _flashMessenger -> addMessage('keine Konsutation zu dieser KonsultationsID', 'error');
					$this -> _redirect('/admin/dashboard/error');
				} else {
					return $this -> _consultation;
				}
			} else {
				$this -> _flashMessenger -> addMessage('KonsultationsID ungültig', 'error');
				$this -> _redirect('/admin/dashboard/error');
			}
		}
	}

	/**
	 * getQid
	 * checks the qid
	 * @param qid, get param
	 * @return (int)
	 *
	 **/
	protected function getQid($params) {
		if (isset($params["qid"])) {
			$isDigit = new Zend_Validate_Digits();
			if ($params["qid"] > 0 && $isDigit -> isValid($params["qid"])) {
				return (int)$params["qid"];
			} else {
				$this -> _flashMessenger -> addMessage('QuestionID ungültig', 'error');
				$this -> _redirect('/admin/dashboard/error');
			}
		}
	}

	/**
	 * getDirId
	 * checks the id
	 * @param dir, get param
	 * @return (int)
	 *
	 **/
	protected function getDirId($params) {
		if (isset($params["dir"])) {
			$isDigit = new Zend_Validate_Digits();
			if ($params["dir"] > 0 && $isDigit -> isValid($params["dir"])) {
				return (int)$params["dir"];
			} else {
				$this -> _flashMessenger -> addMessage('DirectoryID ungültig', 'error');
				$this -> _redirect('/admin/dashboard/error');
			}
		}
	}

	/**
	 * getTId
	 * checks the tid
	 * @param tid, get params
	 * @return (int)
	 *
	 **/
	protected function getTId($params) {
		if (isset($params["tid"])) {
			$isDigit = new Zend_Validate_Digits();
			if ($params["tid"] > 0 && $isDigit -> isValid($params["tid"])) {
				return (int)$params["tid"];
			} else {
				$this -> _flashMessenger -> addMessage('Thesen ID ungültig', 'error');
				$this -> _redirect('/admin/dashboard/error');
			}
		}
	}

}
