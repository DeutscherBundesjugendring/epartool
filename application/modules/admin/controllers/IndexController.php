<?php

class Admin_IndexController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;


    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    /**
     * Shows the list of consultations
     */
    public function indexAction()
    {
        $this->view->consultations = (new Model_Consultations())->getWithInputsAndContribs(5, 5);
    }
}
