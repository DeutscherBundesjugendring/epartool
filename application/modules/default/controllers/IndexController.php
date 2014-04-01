<?php
/**
 * IndexController
 * @desc         public area
 * @author                Jan Suchandt
 */
class IndexController extends Zend_Controller_Action
{
    protected $_auth = null;

    protected $_flashMessenger = null;

    /**
     * Construct
     * @see Zend_Controller_Action::init()
     * @return void
     */
    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();
        $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
    }
    /**
     * home
     * @desc landingpage
     * @return void
     */
    public function indexAction()
    {
        // get last 3 consultations
        $con = new Model_Consultations();
        $conList = $con->getLast();

        $this->view->assign(array('consultations' => $conList));
    }

    /** Migrate tags in csv-form from table inputs
     * to db-relation table inpt_tgs
     * DONT USE IN LIVE-SYSTEM
     */
    /*
    public function migratetagsAction()
    {
        echo('Tag-Input-Relation von CSV-Format zur DB-Relation migrieren');

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $inputModel = new Model_Inputs();
        $inputModel->migrateTags();
    }
    */

    /**
     * Loginform
     * @desc action you reach with login-form
     * @return void
     */
//    public function loginAction() {
//        $form = new Form_Login();
//        $auth = Zend_Auth::getInstance();
//        if ($auth->hasIdentity()) {
//            return $this->_forward('index');
//        }
//    }

    /**
     * Accept search querry and display result
     */
    public function searchAction()
    {
        $needle = $this->getRequest()->getParam('q', 0);

        if ($needle) {
            $filterChain = new Zend_Filter();
            $filterChain->appendFilter(new Zend_Filter_StringTrim());
            $filterChain->appendFilter(new Zend_Filter_StringToLower(array('encoding' => 'UTF-8')));
            $filterChain->appendFilter(new Zend_Filter_HtmlEntities());
            $needle = $filterChain->filter($needle);

            // Search in articles with no consultations ("grundinformationen")
            $articles = new Model_Articles();
            $consultation = new Model_Consultations();
            $followUps = new Model_Followups();

            $this->view->needle = $needle;
            $this->view->resultsGeneral = $articles->search($needle);
            $this->view->resultsConsultations = $consultation->search($needle);
            $this->view->resultsFollowUps = $followUps->search($needle);
        } else {
            $this->redirect('');
        }

    }
}
