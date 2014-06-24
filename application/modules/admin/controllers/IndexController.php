<?php
/**
 * IndexController
 *
 * @desc     administrationareas
 * @author                Jan Suchandt
 */

class Admin_IndexController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;

    /**
     * @desc Construct
     * @return void
     */
    public function init()
    {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    /**
     * @desc admin dashboard
     * @return void
     */
    public function indexAction()
    {
        $consultationsModel = new Model_Consultations();
        $consultations = $consultationsModel->getAll();

        $inputsModel = new Model_Inputs();
        $inputs = $inputsModel->getLast(10);

        $this->view->assign(array(
            'consultations' => $consultations,
            'inputs' => $inputs,
        ));
    }
}
