<?php

/**
 * Description of Admin_FollowupController
 *
 * @author Marco Dinnbier
 */

class Admin_FollowupController extends Zend_Controller_Action {
   
   protected $_flashMessenger = null;
  
   protected $_consultation = null;
   private $kid;
    
    /**
   * Construct
   * @see Zend_Controller_Action::init()
   * @return void
   */    
    public function init() {
        $this->_helper->layout->setLayout('backend');
        $this->_flashMessenger =
        $this->_helper->getHelper('FlashMessenger');
        $kid = $this->_request->getParam('kid', 0);
        if ($kid > 0) {
          $consultationModel = new Model_Consultations();
          $this->_consultation = $consultationModel->find($kid)->current();
          $this->kid = $kid;
          $this->view->consultation = $this->_consultation;
        }
    }
    
    /*
     * index
     * Follow-up Files Overview
     * 
     * @param $_GET['kid'] integer consultation id
     */
    public function indexAction(){
        $kid = $this->getRequest()->getParam('kid', 0);
        $followupFiles = new Model_FollowupFiles();        
        $this->view->followupFiles = $followupFiles->getByKid($kid,'when DESC');
        
    }
    
    /*
     * createSnippet
     * create new snippet in fowups after param prev
     * 
     * @param $_GET['kid'] integer consultation id
     * @param $_GET['ffid'] integer fowup_fls.ffid
     * @param $_GET['prev'] integer prev
     * 
     */
    public function createSnippetAction(){
        
        
        $kid = $this->getRequest()->getParam('kid', 0);
        $ffid = $this->getRequest()->getParam('ffid', 0);
        $prev = $this->getRequest()->getParam('prev', 0);
        
        $form = new Admin_Form_Followup_Snippet();               
        $form->setAction($this->view->baseUrl() . '/admin/followup/create-snippet/kid/' . $this->kid . '/ffid/'. $ffid . '/prev/' . $prev );

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
               
                $followups = new Model_Followups();
                $followupsRow = $followups->createRow($form->getValues());
                $followupsRow->ffid = $ffid;
                
                $newId = $followupsRow->save();
              
                if ($newId > 0) {
                  $followupFiles = new Model_FollowupFiles();
                  $followupRowset = $followupFiles->getFollowupsById($ffid, 'docorg ASC');                  

                  $i = 1;                  
                  foreach ($followupRowset as $followupRow) {
                     $org = $i;
                     if ($followupRow->docorg == 0){
                        if ($prev == 0) {
                            $i++;
                        } else {$org = $prev + 1;}
                       
                     } else {
                         if ($followupRow->docorg == $prev  ) { $i++;}                         
                         $i++;
                     }   
                     
                     $followupRow->docorg = $org;
                     $followupRow->save();
                  }
                
                } else {
                  $this->_flashMessenger->addMessage('Erstellen eines neuen Follow-up-Dokuments fehlgeschlagen!', 'error');
                }

                $this->_redirect($this->view->url(array(
                  'action' => 'edit-file',
                  'kid' => $this->kid,
                  'ffid' => $ffid
                )), array('prependBase' => false));
            } else {
              $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
              $form->populate($form->getValues());
            }
        }

         $this->view->assign(array(
          'kid' => $kid,
          //'consultation' => $consultation,
          'form' => $form,
          'ffid' => $ffid
        ));
        
    }
    
    /*
     * createFile
     * create new file in fowup_fls
     */
    public function createFileAction(){
        $form = new Admin_Form_Followup_File();
       // $popuplink = new Admin_Form_Decorator_Popuplink();
        
        $form->setAction($this->view->baseUrl() . '/admin/followup/create-file/kid/' . $this->kid);

        
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
               
                $followupFiles = new Model_FollowupFiles();
                $followupFilesRow = $followupFiles->createRow($form->getValues());
                $followupFilesRow->kid = $this->kid;
               // Zend_Debug::dump($followupFilesRow);
                $newId = $followupFilesRow->save();
               // Zend_Debug::dump($newId);
                if ($newId > 0) {
                  $this->_flashMessenger->addMessage('Neues Follow-up-Dokument wurde erstellt.', 'success');
                } else {
                  $this->_flashMessenger->addMessage('Erstellen eines neuen Follow-up-Dokuments fehlgeschlagen!', 'error');
                }

              $this->_redirect($this->view->url(array(
                'action' => 'index',
                'kid' => $this->kid
              )), array('prependBase' => false));
            } else {
              $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben!', 'error');
              $form->populate($form->getValues());
            }
        }
        
    }
    
    /*
     * editSnippet
     * edit snippet in fowups
     * 
     * @param $_GET['kid'] integer consultation id
     * @param $_GET['ffid'] integer fowup_fls.ffid
     * @param $_GET['fid'] integer fowup_fls.fid
     * 
     */
    public function editSnippetAction(){
        $kid = $this->getRequest()->getParam('kid', 0);
        $fid = $this->getRequest()->getParam('fid', 0);
        $ffid = $this->getRequest()->getParam('ffid', 0);

        if ($fid > 0) {
          $followups = new Model_Followups();
          $followupsRow = $followups->find($fid)->current();
          $form = new Admin_Form_Followup_Snippet();
          
           if ($this->getRequest()->isPost()) {
            // Formular wurde abgeschickt und muss verarbeitet werden
            $params = $this->getRequest()->getPost();
            if ($form->isValid($params)) {
              $followupsRow->setFromArray($form->getValues());
              $followupsRow->save();
              $this->_flashMessenger->addMessage('Änderungen wurden gespeichert.', 'success');
              $followup = $followupsRow->toArray();
              
              $this->_redirect($this->view->url(array(
                'action' => 'edit-file',
                'kid' => $this->kid,
                'ffid' => $ffid
              )), array('prependBase' => false));
            } else {
              $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben und versuchen Sie es erneut!', 'error');
              $followup = $params;
            }
          } else {
            $followup = $followups->getById($fid);
          }
          $form->populate($followup);
        }
    
        foreach ($form->getElements() as $element) {
          $element->clearFilters();
          $element->setValue(html_entity_decode($element->getValue()));
        }

        $this->view->assign(array(
          'kid' => $kid,
          //'consultation' => $consultation,
          'form' => $form,
          'fid' => $fid,  
          'followups' => $followups
        ));
        
    }
    
     /*
     * editSnippet
     * edit doc in fowup_fls
     * 
     * @param $_GET['kid'] integer consultation id
     * @param $_GET['ffid'] integer fowup_fls.ffid
     * @param $_GET['movefid'] integer fowups.fid of while which moved
     * 
     */
    public function editFileAction(){
        $kid = $this->getRequest()->getParam('kid', 0);
        $ffid = $this->getRequest()->getParam('ffid', 0);
        $movefid = $this->getRequest()->getParam('movefid',0);
        $movefollowup = NULL;
        if ($ffid > 0) {
            
          $Model_Followups = new Model_Followups();
          $Model_FollowupFiles = new Model_FollowupFiles();
          $followupFilesRow = $Model_FollowupFiles->find($ffid)->current();
          $form = new Admin_Form_Followup_File();
          
          
          if ($movefid > 0) {
               $movefollowup = $Model_Followups->getById($movefid);
          }  
          
          $followups = array();
          $result = $Model_FollowupFiles->getFollowupsById($ffid, 'docorg ASC')->toArray();
          
          foreach ($result as $followup) {
              $rel = $Model_Followups->getRelated($followup['fid']);
              $snippet = $followup;
              $snippet['relFowupCount'] = $rel['count'];
              $followups[] = $snippet;              
          }
         
          
          if ($this->getRequest()->isPost()) {
            // Formular wurde abgeschickt und muss verarbeitet werden
            $params = $this->getRequest()->getPost();
            if ($form->isValid($params)) {
              $followupFilesRow->setFromArray($form->getValues());
              $followupFilesRow->save();
              $this->_flashMessenger->addMessage('Änderungen wurden gespeichert.', 'success');
              $followupFile = $followupFilesRow->toArray();
            } else {
              $this->_flashMessenger->addMessage('Bitte prüfen Sie Ihre Eingaben und versuchen Sie es erneut!', 'error');
              $followupFile = $params;
            }
          } else {
            $followupFile = $Model_FollowupFiles->getById($ffid);
          }
          $form->populate($followupFile);
        }
    
        foreach ($form->getElements() as $element) {
          $element->clearFilters();
          $element->setValue(html_entity_decode($element->getValue(), ENT_COMPAT, 'UTF-8'));
        }

        $this->view->assign(array(
          'kid' => $kid,
          //'consultation' => $consultation,
          'form' => $form,
          'ffid' => $ffid,  
          'followups' => $followups,
          'movefollowup' => $movefollowup
        ));
        
    }
    
    /*
     * deleteFile
     * delete doc in fowup_fls
     * 
     * @param $_GET['kid'] integer consultation id
     * @param $_GET['ffid'] integer fowup_fls.ffid
     * 
     */
    public function deleteFileAction(){
        $kid = $this->getRequest()->getParam('kid', 0);
        $ffid = $this->getRequest()->getParam('ffid', 0);
        if ($ffid > 0) {
          $followupFiles = new Model_FollowupFiles();
          $followupFilesRow = $followupFiles->getById($ffid);
          if ($followupFilesRow['kid'] == $kid) {
            $nrDeleted = $followupFiles->deleteById($ffid);
            if ($nrDeleted > 0) {
              $this->_flashMessenger->addMessage('Das Follow-up-Dokument wurde gelöscht.', 'success');
            } else {
              $this->_flashMessenger->addMessage('Das Follow-up-Dokument konnte nicht gelöscht werden.', 'error');
            }
          }
        }
        $this->_redirect('/admin/followup/index/kid/' . $kid);
    }
    
    
    /*
     * deleteSnippet
     * delete snippet in fowups and reorder snippets
     * 
     * @param $_GET['kid'] integer consultation id
     * @param $_GET['ffid'] integer fowup_fls.ffid
     * @param $_GET['fid'] integer fowups.fid
     * 
     */
    public function deleteSnippetAction(){
        $kid = $this->getRequest()->getParam('kid', 0);
        $fid = $this->getRequest()->getParam('fid', 0);
        $ffid = $this->getRequest()->getParam('ffid', 0);
        
        if ($fid > 0) {
          $followups = new Model_Followups();          
          $nrDeleted = $followups->deleteById($fid);
            if ($nrDeleted > 0) {
              $followupFiles = new Model_FollowupFiles();
              $followupRowset = $followupFiles->getFollowupsById($ffid, 'docorg ASC'); 
                    $i = 1;                  
                  foreach ($followupRowset as $followupRow) {                     
                     $followupRow->docorg = $i;
                     $followupRow->save();
                     $i++;
                  }
              $this->_flashMessenger->addMessage('Das Follow-up wurde gelöscht.', 'success');
            } else {
              $this->_flashMessenger->addMessage('Das Follow-up konnte nicht gelöscht werden.', 'error');
            }
          
        }
        $this->_redirect($this->view->url(array(
                'action' => 'edit-file',
                'kid' => $this->kid,
                'ffid' => $ffid
              )), array('prependBase' => false));
        
    }
    
    /*
     * move
     * move snippet in fowups 
     * 
     * @see editFileAction
     * 
     * @param $_GET['kid'] integer consultation id
     * @param $_GET['ffid'] integer fowup_fls.ffid
     * @param $_GET['fid'] integer fowups.fid
     * @param $_GET['movefid'] integer fowups.fid of moved snippet
     * @param $_GET['prev'] integer move after prev (docorg of previous snippet)
     * 
     */
    public function moveAction() {
        $kid = $this->getRequest()->getParam('kid', 0);
        $fid = $this->getRequest()->getParam('fid', 0);
        $ffid = $this->getRequest()->getParam('ffid', 0);
        $movefid = $this->getRequest()->getParam('movefid', 0);
        $prev = $this->getRequest()->getParam('prev');
        
        if ($movefid > 0) {        
            
            $Model_Followup = new Model_Followups();
            $followupFiles = new Model_FollowupFiles();
            $followupsByFile = $followupFiles->getFollowupsById($ffid, 'docorg ASC')->toArray();
            $arr = array();
            if ($prev == 0) array_push ($arr, $Model_Followup->find($movefid)->current()->toArray());
            foreach ($followupsByFile as $followup) {
                           
                if ($followup['fid'] != $movefid) array_push ($arr, $followup);
                if ($followup['docorg'] == $prev) array_push ($arr, $Model_Followup->find($movefid)->current()->toArray());
            }
            $i=1;
            foreach ($arr as $followup) {
               $followupsRow = $Model_Followup->find($followup['fid'])->current();
               $followupsRow->docorg = $i;
               $followupsRow->save();               
               $i++;                   
            }
            
               $this->_redirect($this->view->url(array(
                'module' => 'admin',
                'controller' => 'followup',
                'action' => 'edit-file',
                'kid' => $this->kid,
                'ffid' => $ffid
                
              ),null,true), array('prependBase' => false));    
            
        } else {

        $this->_redirect($this->view->url(array(
                'action' => 'edit-file',
                'kid' => $this->kid,
                'ffid' => $ffid,
                'movefid' => $fid 
                
              )), array('prependBase' => false));
            
        }    
    }
    
    /*
     * hierarchy
     * increment/decrement hierarchy level for snippets in fowups 
     *
     * 
     * @param $_GET['kid'] integer consultation id
     * @param $_GET['ffid'] integer fowup_fls.ffid
     * @param $_GET['fid'] integer fowups.fid
     * @param $_GET['hlvl'] integer fowups.hlvl
     * 
     */
    public function hierarchyAction() {
        
        $kid = $this->getRequest()->getParam('kid', 0);
        $fid = $this->getRequest()->getParam('fid', 0);
        $ffid = $this->getRequest()->getParam('ffid', 0);
        $hlvl = $this->getRequest()->getParam('hlvl', 0);
        
        if ($hlvl > 0 && $hlvl < 7 && $fid > 0) {
             $Model_Followup = new Model_Followups();
             $followupsRow = $Model_Followup->find($fid)->current();
             $followupsRow->hlvl = $hlvl;
             $followupsRow->save();
        }
        $this->_redirect($this->view->url(array(
                'action' => 'edit-file',
                'kid' => $this->kid,
                'ffid' => $ffid
                
              )), array('prependBase' => false));
    }
    
    public function referenceAction(){
        
        $this->_helper->layout->setLayout('popup');
        $Model_Followups = new Model_Followups();
        $Model_Inputs = new Model_Inputs();
        $Model_FollowupFiles = new Model_FollowupFiles();
        $Model_Questions = new Model_Questions();   
        
        $kid = $this->getRequest()->getParam('kid', 0);
        $fid = $this->getRequest()->getParam('fid', 0);
        
        $docs = array();
        $snippets = array();
        
        if ($kid > 0) {
            $ffid_array = array();
            
            $docs = $Model_FollowupFiles->getByKid($kid,'when DESC');
            foreach ($docs as $doc) {
                $ffid_array[] = $doc['ffid'];
            }
            $snippets = $Model_Followups->getByDocIdArray($ffid_array);
        }
        
        if ($this->getRequest()->isPost()) {
            
            $params = $this->getRequest()->getPost();
            Zend_Debug::dump($params);
            if (!empty($params['question'])) {
               
                $question = $Model_Questions->getById($params['question']);
            }
            if (!empty($params['chosenDoc'])) {
               
                $chosenDoc = $params['chosenDoc'];
            }
            
            $Model_FollowupsRef = new Model_FollowupsRef();
            
            if (!empty($params['inp_list']) && !empty($params['insert_inputs'])) {
                $inserted = $Model_FollowupsRef->insertBulk($params['inp_list'], $fid, 'tid');
                $message = "$inserted Beiträge wurden zugeordnet.";               
                $this->_flashMessenger->addMessage($message, 'success');
            }
            if (!empty($params['doc_list']) && !empty($params['insert_docs'])) {
                
                $inserted = $Model_FollowupsRef->insertBulk($params['doc_list'], $fid, 'ffid');
                $message = "$inserted Dokumente wurden zugeordnet.";     
                $this->_flashMessenger->addMessage($message, 'success');
            }
            if (!empty($params['snippet_list']) && !empty($params['insert_snippets'])) {
                
                $inserted = $Model_FollowupsRef->insertBulk($params['snippet_list'], $fid, 'fid');
                $message = "$inserted Snippets wurden zugeordnet.";
                $this->_flashMessenger->addMessage($message, 'success');
            }
            
           
        }
        
        $related = $Model_Followups->getRelated($fid);
        $followup = $Model_Followups->getById($fid);
        
        if (empty($question)) {
           // get first question of this consultation
           $questionRow = $Model_Questions->getByConsultation($kid)->current();
           $question = $Model_Questions->getById($questionRow->qi);
        }
        if (empty($chosenDoc)) {
           // get first question of this consultation
           $followupFile = $Model_FollowupFiles->getByKid($kid,'when DESC',NULL, $followup['ffid']);
           $chosenDoc = $followupFile[0]['ffid'];

        }
       // Zend_Debug::dump($question);
        $this->view->assign(array(
          'kid' => $kid,
          'followup' => $followup,          
          'related' => $related,
          'snippets' => $snippets,
          'docs' => $docs,
          'question' => $question,
          'chosenDoc' => $chosenDoc
        ));
    }
    
    public function delReferenceAction () {
        $kid = $this->getRequest()->getParam('kid', 0);
        $fid_ref = $this->getRequest()->getParam('fid_ref', 0);
        $fid = $this->getRequest()->getParam('fid', 0);
        $tid = $this->getRequest()->getParam('tid', 0);
        $ffid = $this->getRequest()->getParam('ffid', 0);
        
        if ($fid > 0) {
            
            $reftype = 'fid';
            $refkey = $fid;
        }
        if ($tid > 0) {
            
            $reftype = 'tid';
            $refkey = $tid;
        }
        if ($ffid > 0) {
            
            $reftype = 'ffid';
            $refkey = $ffid;
        }
        
        
       $Model_FollowupsRef = new Model_FollowupsRef();
       $Model_FollowupsRef->deleteRef($fid_ref, $reftype, $refkey);
       
         
        $this->_redirect($this->view->url(array(
                'module' => 'admin',
                'controller' => 'followup',
                'action' => 'reference',
                'kid' => $kid,
                'fid' => $fid_ref
              ),null,true));
    }
}

?>
