<?php

class IndexController extends Zend_Controller_Action
{
    protected $_auth = null;

    protected $_flashMessenger = null;


    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();
        $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
    }
    /**
     * The home page
     */
    public function indexAction()
    {
        $conList = (new Model_Consultations())->getLast();
        $this->view->consultations = $conList;
    }

    /**
     * Perform search and display results
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
