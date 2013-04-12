<?php

/**
 * Description of FollowupController
 *
 * @author Marco Dinnbier
 */
class FollowupController extends Zend_Controller_Action {

    /**
     * Construct
     * @see Zend_Controller_Action::init()
     * @return void
     */
    public function init() {
         $kid = $this->getRequest()->getParam('kid', 0);
         $consultationModel = new Model_Consultations();
         $consultation = $consultationModel->find($kid)->current();
    
         $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
         
          if ($consultation) {
            $this->kid = $kid;
            $this->_consultation = $consultation;
            $this->view->consultation = $consultation;
            $this->view->media_cnslt_dir = $this->view->baseUrl().'/media/consultations/'.$kid.'/';
            
          } else {
            $action = $this->_request->getActionName();
            if ($action != 'support') {
              $this->_flashMessenger->addMessage('Keine Konsultation angegeben!', 'error');
              $this->_redirect('/');
            }
          }
    }

    public function indexAction() {
        
        $followupModel = new Model_Followups();
        $this->view->latest_followups = $followupModel->getByKid($this->kid,'when DESC',5);
       
    }
    public function showAction() {
        
        $qid = $this->getRequest()->getParam('qid', 0);
        $questionModel = new Model_Questions();
        $question = $questionModel->getById($qid);
        $this->view->question = $question;
        
    }

}

?>
