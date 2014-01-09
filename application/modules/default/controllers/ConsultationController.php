<?php
/**
 * ConsultationController
 *
 */
class ConsultationController extends Zend_Controller_Action
{
    protected $_user = null;

    /**
     * Construct
     * @see Zend_Controller_Action::init()
     * @return void
     */
    public function init()
    {
    }
    /**
     * List all public consultations or redirect to index page of specific
     * consultation if kid given
     *
     */
    public function indexAction()
    {
        $kid = $this->_request->getParam('kid', 0);
        if ($kid > 0) {
            $this->redirect('/article/index/kid/' . $kid);
        }
        $consultationModel = new Model_Consultations();

        $this->view->consultations = $consultationModel->getPublic();
    }
}
