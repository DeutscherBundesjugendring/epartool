<?php
/**
 * UserController
 *
 * @desc   Users for Consultation
 */
class Admin_DirectoriesController extends Zend_Controller_Action
{
    protected $_flashMessenger;

    /**
     * Construct
     * @return void
     */
    public function init()
    {
        // Setzen des Standardlayouts
        $this -> _helper -> layout -> setLayout('backend');
        $this -> _flashMessenger = $this -> _helper -> getHelper('FlashMessenger');
        $kid = $this -> _request -> getParam('kid', 0);
        $this -> data = $this -> _request -> getPost();
        $this -> data["kid"] = $kid;
        if ($kid > 0) {
            $consultationModel = new Model_Consultations();
            $this -> _consultation = $consultationModel -> find($kid) -> current();
            $this -> view -> consultation = $this -> _consultation;
        } else {
            $this -> _flashMessenger -> addMessage('Keine Konsultation angegeben!', 'error');
            $this -> redirect('/admin');
        }
    }

    /**
     *  indexAction()
     *  create  the form for directories
     * @param get param
     * @return
     *
     **/
    public function indexAction()
    {
        $dirs = array();
        $selectDirs = array();

        $directories = new Model_Directories();
        $dirs = $directories -> getTree("node.kid = ".$this -> _consultation -> kid." AND parent.kid = ".$this -> _consultation -> kid."")-> toArray();

        if (count($dirs) === 0)
            $selectDirs = array("Noch kein Order zur Auswahl");
        foreach ($dirs as $key => $value) {
            $selectDirs[$value["id"]] = str_repeat('-', (int) $value['depth'] * 2) . ' ' . $value['dir_name'];
        }
        $createNewForm = new Admin_Form_Directory();
        $createNewForm -> setAction($this -> view -> baseUrl() . '/admin/directories/create/kid/' . $this -> _consultation -> kid);
        $createNewForm -> parent -> addMultiOptions($selectDirs);

        $this -> view -> createNewForm = $createNewForm;
        $this -> view -> directories = $dirs;

    }

    /**
     *  createAction()
     *  insert new directory in DB
     * @param post param
     * @return redirect to indexAction
     *
     **/
    public function createAction()
    {
        $this -> isMethodPost();
        $this -> _helper -> viewRenderer -> setNoRender(true);

        $parent = $this -> data["parent"];
        $position = $this -> data["position"];
        $this -> data = $this -> getDbVars();

        if ($this -> _request -> isPost()) {
            if (!empty( $this -> data["dir_name"])) {
                $this -> data = $this -> getDbVars();
                $directories = new Model_Directories();
                if ($parent == 0) {
                    $directories -> insert($this -> data, $parent, NP_Db_Table_NestedSet::NEXT_SIBLING);
                } else {
                    if ($position == "FIRST_CHILD")
                        $directories -> insert($this -> data, $parent, NP_Db_Table_NestedSet::FIRST_CHILD);
                    if ($position == "LAST_CHILD")
                        $directories -> insert($this -> data, $parent, NP_Db_Table_NestedSet::LAST_CHILD);
                    if ($position == "NEXT_SIBLING")
                        $directories -> insert($this -> data, $parent, NP_Db_Table_NestedSet::NEXT_SIBLING);
                    if ($position == "PREV_SIBLING")
                        $directories -> insert($this -> data, $parent, NP_Db_Table_NestedSet::PREV_SIBLING);
                }
                $this -> _flashMessenger -> addMessage('Ordner "' . $this -> data["dir_name"] . '" angelegt', 'success');
                $this -> redirect('/admin/directories/index/kid/' . $this -> _consultation -> kid);

            } else {
                $this -> _flashMessenger -> addMessage('Fehler bei der Eingabe', 'error');
                $this -> redirect('/admin/directories/index/kid/' . $this -> _consultation -> kid);
            }
        }

    }

    public function editAction()
    {
    }

    /**
     *  deleteAction()
     *  remove directory from DB
     * @param post param
     * @return redirect to indexAction
     * @todo beiträge auf default setzten
     **/
    public function deleteAction()
    {
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $node = (int) $this -> _request -> getParam('dir', 0);
        $directories = new Model_Directories();
        $directories -> deleteNode($node, true);
        $this -> _flashMessenger -> addMessage('Ordner gelöscht!', 'success');
        $this -> redirect('/admin/directories/index/kid/' . $this -> _consultation -> kid);

    }

    /**
     *   isMethodPost()()
     *  helper for REQUEST_METHOD == POST
     * @see createAction()
     * @param post param
     * @return redirect to indexAction if false
     **/
    protected function isMethodPost()
    {
        if (!$this -> _request -> isPost()) {
            $this -> _flashMessenger -> addMessage('/admin/directories/index/kid/' . $this -> _consultation -> kid, 'error');
            $this -> redirect('/admin/directories/index/kid/' . $this -> _consultation -> kid);
        }
    }

    /**
     *   getDbVars()
     *  set params for database insert
     * @see createAction()
     * @param post param from init()
     * @return returns the array for insert in database
     **/
    protected function getDbVars()
    {
        $array = array('dir_name' => $this -> data["dir_name"], "kid" => (int) $this -> data["kid"]);

        return $array;
    }

}
