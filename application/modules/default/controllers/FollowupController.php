<?php

/**
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
         
            $this->view->consultation = $consultation;            
            $this->view->media_cnslt_dir = $this->view->baseUrl().'/media/consultations/'.$kid.'/';
            
         } else {
            $action = $this->_request->getActionName();
            if ($action != 'like' && $action != 'unlike') {
              $this->_flashMessenger->addMessage('Keine Konsultation angegeben!', 'error');
              $this->_redirect('/');
            }
         }
    }
    /**
     * Followups Page: Index 
     * assigns the latest FollowupDocs by kid from fowup_fls 
     * 
     * @name index
     * @param $_GET['kid'] consultation id
     * @return void
     */
    public function indexAction() {
        $kid = $this->_getParam('kid', 0);
        $followupModel = new Model_FollowupFiles();
        $this->view->latest_followups = $followupModel->getByKid($kid,'when DESC',5);
    }
    
    /**
     * Followups Page: Inputs by Questions
     * assigns the data for questions and inputs
     * 
     * @param $_GET['qid'] question id 
     * @param $_GET['kid'] consultation id
     * 
     * @return void
     */
    public function byQuestionAction() {
        $kid = $this->_getParam('kid', 0);
        $qid = $this->getRequest()->getParam('qid', 0);
        
        $inputModel = new Model_Inputs();
        $questionModel = new Model_Questions();
        
        if (empty($qid)) {
           // get first question of this consultation
           $questionRow = $questionModel->getByConsultation($kid)->current();
           $qid = $questionRow->qi;
        }
        
        $this->view->numberInputs = $inputModel->getCountByQuestion($qid);
        $this->view->question = $questionModel->getById($qid);
        
        $paginator = Zend_Paginator::factory($inputModel->getSelectByQuestion($qid));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;
        
    }
    
    /*
     * shows the initial timeline for followups
     * 
     * @param $_GET['kid'] consultation id
     * @param $_GET['qid'] question id 
     * @param $_GET['tid'] input id
     * 
     * @return void
     */
    
    public function showAction() {
        $kid = $this->_getParam('kid', 0);
        $qid = $this->_getParam('qid', 0);
        $tid = $this->_getParam('tid', 0);
        
        $Model_Inputs = new Model_Inputs();
        $Model_Questions = new Model_Questions();
        $Model_Followups = new Model_Followups();
        
      
        $question = $Model_Questions->getById($qid);


            
            $input = $Model_Inputs->getById($tid);
            $input['relFowupCount'] = count($Model_Followups->getByInput($tid));

        
        $result = $Model_Inputs->getRelatedWithVotesById($tid);
        $relInputs = array();
        foreach ($result as $relInput) {
            $relInput['relFowupCount'] = count($Model_Followups->getByInput($relInput['tid']));
            $relInputs[] = $relInput;
        }
    
        $this->view->assign(array(
          'question' => $question,
          'input' => $input,
          'relInput' => $relInputs,
        ));
    }
    /*
     * 
     * sends jsondata
     *
     * @param $_GET['kid'] consultation id
     * @param $_GET['tid'] show followups by fowup_rid.tid
     * @param $_GET['fid'] show References by fowups.fid
     * @param $_GET['ffid'] show followup_fls by followup_fls.ffid
     * @return void
     * 
     */
    public function jsonAction(){
        $kid = $this->_getParam('kid', 0);
        $tid = $this->_getParam('tid', 0);
        $fid = $this->_getParam('fid', 0);
        $ffid = $this->_getParam('ffid', 0);
        $data = array();
        
        //show followups by fowup_rid.tid
        if ($tid > 0) {
            
            $Model_Followups = new Model_Followups();
            $followups = $Model_Followups->getByInput($tid);
            $data['byinput'] = $followups;
            
        }
        //show References by fowups.fid
        if ($fid > 0) {
            
            $Model_Followups = new Model_Followups();
            $Model_FollowupFiles = new Model_FollowupFiles();
            $related= $Model_Followups->getRelated($fid);
            $docs = array();
            $snippets = array();
            
            foreach ($related as $item) {
                
                if($item['fid']){
                    $snippet = $Model_Followups->getById ($item['fid']);
                    $snippet['relFowupCount'] = count($Model_Followups->getRelated($item['fid']));
                    $snippets[] = $snippet;
                } 
                if($item['ffid']) $docs[] = $Model_FollowupFiles->getById ($item['ffid']);
            }
            $data['refs']['snippets'] = $snippets;
            $data['refs']['docs'] = $docs;
        }

        //show followup_fls by followup_fls.ffid
        if ($ffid > 0) {
            
            $Model_FollowupFiles = new Model_FollowupFiles();
            $data['bydoc'] = $Model_FollowupFiles->getById($ffid);
            $data['mediafolder'] = $this->view->baseUrl().'/media/consultations/'.$kid.'/';
           
        }
        
        $this->_helper->json->sendJson($data);        
    }
    
    /*
     * like a followup-snippet
     * checks if UserAgent+IP combination has liked/unliked
     * sends json with like/unlike count after database update
     * 
     * @param $_GET['fid'] fowup.fid
     * 
     * @return void
     */
    public function likeAction(){
        $fid = $this->getRequest()->getParam('fid', 0);
      
        $followups = new Model_Followups();
        $result = $followups->supportById( $fid, 'lkyea' );   
        if ($result == 0) {            
             $data = array( 'error' => 'Follow-up ID nicht korrekt.' );
        } else {
             $data = array( 'lkyea' => $result ); 
        }            
        $this->_helper->json->sendJson($data);
        
    }
    
    /*
     * unlike a followup-snippet
     * checks if UserAgent+IP combination has liked/unliked
     * sends json with like/unlike count after database update
     * 
     * @param $_GET['fid'] fowup.fid
     * 
     * @return void
     */
    public function unlikeAction(){
        $fid = $this->getRequest()->getParam('fid', 0);
      
        $followups = new Model_Followups();
        $result = $followups->supportById( $fid, 'lknay' );
        if ($result == 0) {
             $data = array( 'error' => 'Follow-up ID nicht korrekt.' );
        } else {
             $data = array( 'lkynay' => $result );        
        }
        $this->_helper->json->sendJson($data);
        
    }
}

?>
